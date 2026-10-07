<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use InvalidArgumentException;

class FileUploadService
{
    /**
     * Procesa, optimiza y sube un archivo codificado en Base64 a S3 (o disco public de respaldo).
     *
     * @param  string  $base64String  Contenido base64 con o sin prefijo data URI
     * @param  string  $folder        Carpeta destino dentro del bucket/storage
     * @return array{path: string, url: string, disk: string}
     *
     * @throws InvalidArgumentException|Exception
     */
    public static function uploadOptimizedBase64(string $base64String, string $folder = 'invoices'): array
    {
        $mime = null;

        // 1. Extraer encabezado data:mime/type;base64, si existe
        if (preg_match('/^data:([a-zA-Z0-9\/\+.-]+);base64,/', $base64String, $matches)) {
            $mime = strtolower(trim($matches[1]));
            $base64String = substr($base64String, strpos($base64String, ',') + 1);
        }

        // Eliminar saltos de línea y espacios en blanco accidentales
        $base64String = preg_replace('/\s+/', '', $base64String);

        $binaryData = base64_decode($base64String, true);
        if ($binaryData === false) {
            throw new InvalidArgumentException('El archivo base64 enviado es inválido o no se pudo decodificar.');
        }

        $fileName = Str::uuid()->toString();

        // 2. Verificar si es un archivo PDF
        $isPdf = ($mime === 'application/pdf') || (substr($binaryData, 0, 4) === '%PDF');

        if ($isPdf) {
            // Límite de seguridad para PDFs: 5MB
            if (strlen($binaryData) > 5 * 1024 * 1024) {
                throw new InvalidArgumentException('El archivo PDF excede el tamaño máximo permitido de 5MB.');
            }
            $fileContent = $binaryData;
            $extension = 'pdf';
            $contentType = 'application/pdf';
        } else {
            // 3. Es una imagen: optimizar con Intervention Image
            try {
                if (class_exists(Image::class)) {
                    $image = Image::make($binaryData);
                } else {
                    $manager = new \Intervention\Image\ImageManager(['driver' => 'gd']);
                    $image = $manager->make($binaryData);
                }

                // Corregir orientación basada en metadatos EXIF si proviene de teléfono móvil
                if (method_exists($image, 'orientate')) {
                    $image->orientate();
                }

                // Redimensionar si supera 1600px de ancho o alto para evitar archivos pesados
                $maxDimension = 1600;
                if ($image->width() > $maxDimension || $image->height() > $maxDimension) {
                    $image->resize($maxDimension, $maxDimension, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                // Codificar en formato JPG optimizado con 75% de calidad
                $fileContent = (string) $image->encode('jpg', 75);
                $extension = 'jpg';
                $contentType = 'image/jpeg';
            } catch (Exception $e) {
                Log::warning('Error optimizando imagen base64: '.$e->getMessage());
                throw new InvalidArgumentException('El archivo enviado no corresponde a una imagen válida (JPG, PNG, WEBP) ni a un documento PDF.');
            }
        }

        $filePath = trim($folder, '/').'/'.$fileName.'.'.$extension;

        // 4. Determinar disco de almacenamiento (S3 preferente si hay credenciales configuradas)
        $s3Bucket = config('filesystems.disks.s3.bucket');
        $s3Key = config('filesystems.disks.s3.key');
        $diskName = (! empty($s3Bucket) && ! empty($s3Key)) ? 's3' : 'public';

        try {
            $options = ['visibility' => 'public'];
            if (! empty($contentType)) {
                $options['ContentType'] = $contentType;
            }

            Storage::disk($diskName)->put($filePath, $fileContent, $options);
            $url = Storage::disk($diskName)->url($filePath);
        } catch (Exception $e) {
            // Si S3 falla (por ejemplo credenciales no asignadas aún o error de red),
            // guardamos en el disco local 'public' para no perder el formulario del usuario
            Log::warning("No se pudo subir a S3 ({$e->getMessage()}), guardando en disco local 'public'.");
            $diskName = 'public';
            Storage::disk('public')->put($filePath, $fileContent);
            $url = asset('storage/'.$filePath);
        }

        return [
            'path' => $filePath,
            'url' => $url,
            'disk' => $diskName,
        ];
    }
}
