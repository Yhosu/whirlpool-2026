<?php

namespace App\Console\Commands;

use App\Mail\CanjeAlertMail;
use App\Models\Branch;
use App\Models\Code;
use App\Models\Form;
use App\Models\PrizeBranch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class NotifyCanjesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:canje-alert
                            {--email= : Correo destinatario (por defecto josuandroidg7@gmail.com)}
                            {--form-id= : ID específico del formulario a notificar}
                            {--test : Envía una prueba inmediata usando el canje más reciente}
                            {--limit=10 : Límite de canjes a procesar por ejecución}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica en tiempo real los canjes de premios (informal y ecommerce) con el stock sobrante en números y comprobante adjunto';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $recipients = $this->getRecipients();
        $this->info("Iniciando verificación de canjes. Destinatarios: " . implode(', ', $recipients));

        $stockSummary = $this->calculateStockSummary();

        // 1. MODO PRUEBA
        if ($this->option('test')) {
            return $this->handleTestMode($recipients, $stockSummary);
        }

        // 2. FORMULARIO ESPECÍFICO
        if ($formId = $this->option('form-id')) {
            $form = Form::with(['prize', 'branch', 'sale', 'assignedCode'])->find($formId);
            if (! $form) {
                $this->error("No se encontró ningún formulario con el ID: {$formId}");

                return 1;
            }

            $this->sendNotification($form, $recipients, $stockSummary);
            $this->markAsNotified($form);
            $this->info("Canje {$form->id} notificado exitosamente.");

            return 0;
        }

        // 3. MODO CRON / TIEMPO REAL (FORGE)
        return $this->handlePendingCanjes($recipients, $stockSummary);
    }

    /**
     * Obtiene la lista de destinatarios.
     */
    protected function getRecipients(): array
    {
        if ($emailOpt = $this->option('email')) {
            return array_map('trim', explode(',', $emailOpt));
        }

        if ($envEmails = env('CANJE_ALERT_EMAILS')) {
            return array_map('trim', explode(',', $envEmails));
        }

        $configEmails = config('weekly_report.recipients_canje');
        if (! empty($configEmails) && is_array($configEmails)) {
            return $configEmails;
        }

        return [
            'josuandroidg7@gmail.com',
            'hernan@gooncomunicacion.com',
            'christian@gooncomunicacion.com',
            'sortega@dismac.com.bo',
        ];
    }

    /**
     * Procesa canjes pendientes no notificados.
     */
    protected function handlePendingCanjes(array $recipients, array $stockSummary): int
    {
        $limit = (int) $this->option('limit');
        $hasColumn = Schema::hasColumn('forms', 'canje_notified_at');

        $query = Form::with(['prize', 'branch', 'sale', 'assignedCode'])
            ->whereIn('status', ['used', 'delivered'])
            ->whereNotNull('prize_id')
            ->where('updated_at', '>=', now()->subHours(24));

        if ($hasColumn) {
            $query->whereNull('canje_notified_at');
        }

        $candidates = $query->orderBy('updated_at', 'asc')->take($limit * 3)->get();

        $toNotify = [];
        foreach ($candidates as $cand) {
            if (! $hasColumn && Cache::has('canje_notified_'.$cand->id)) {
                continue;
            }
            $toNotify[] = $cand;
            if (count($toNotify) >= $limit) {
                break;
            }
        }

        if (empty($toNotify)) {
            $this->info('No hay nuevos canjes pendientes por notificar.');

            return 0;
        }

        $this->info('Se encontraron '.count($toNotify).' canje(s) pendiente(s). Procesando...');

        foreach ($toNotify as $form) {
            try {
                $this->sendNotification($form, $recipients, $stockSummary);
                $this->markAsNotified($form);
                $this->info("✓ Notificado canje: Form {$form->id} (CI: {$form->ci_number}, Premio: Bs. ".($form->prize?->amount ?? 0).')');
            } catch (\Exception $e) {
                Log::error('Error enviando notificación de canje: '.$e->getMessage(), ['form_id' => $form->id]);
                $this->error("Error notificando formulario {$form->id}: {$e->getMessage()}");
            }
        }

        return 0;
    }

    /**
     * Envía una notificación de prueba.
     */
    protected function handleTestMode(array $recipients, array $stockSummary): int
    {
        $this->info('Generando envío de prueba...');

        $form = Form::with(['prize', 'branch', 'sale', 'assignedCode'])
            ->whereIn('status', ['used', 'delivered'])
            ->whereNotNull('prize_id')
            ->latest('updated_at')
            ->first();

        if (! $form) {
            $form = Form::with(['prize', 'branch', 'sale', 'assignedCode'])->latest()->first();
        }

        if (! $form) {
            $this->error('No hay ningún formulario registrado en la base de datos para la prueba.');

            return 1;
        }

        $this->info("Usando formulario para prueba: CI {$form->ci_number} ({$form->name}) - Premio: Bs. ".($form->prize?->amount ?? 3000));
        $this->sendNotification($form, $recipients, $stockSummary);
        $this->info("¡Correo de prueba enviado exitosamente a: " . implode(', ', $recipients) . "!");

        return 0;
    }

    /**
     * Envía el correo con comprobante adjunto.
     */
    protected function sendNotification(Form $form, array|string $recipients, array $stockSummary): void
    {
        $tempAttachment = $this->resolveAttachmentPath($form);
        $attachmentPath = $tempAttachment['path'] ?? null;
        $attachmentName = $tempAttachment['name'] ?? null;
        $shouldDelete = $tempAttachment['is_temp'] ?? false;

        try {
            Mail::to($recipients)->send(new CanjeAlertMail($form, $stockSummary, $attachmentPath, $attachmentName));
        } finally {
            if ($shouldDelete && $attachmentPath && file_exists($attachmentPath)) {
                @unlink($attachmentPath);
            }
        }
    }

    /**
     * Marca un formulario como notificado.
     */
    protected function markAsNotified(Form $form): void
    {
        if (Schema::hasColumn('forms', 'canje_notified_at')) {
            $form->update(['canje_notified_at' => now()]);
        } else {
            Cache::forever('canje_notified_'.$form->id, now()->toDateTimeString());
        }
    }

    /**
     * Resuelve el archivo adjunto (recibo o nota de venta).
     */
    protected function resolveAttachmentPath(Form $form): ?array
    {
        // 1. Comprobante en invoice_image (Mercado informal)
        if (! empty($form->invoice_image)) {
            $imagePath = $form->invoice_image;

            // Archivo local en public/storage
            $diskPath = storage_path('app/public/'.$imagePath);
            if (file_exists($diskPath)) {
                return ['path' => $diskPath, 'name' => 'Comprobante_'.$form->ci_number.'.jpg', 'is_temp' => false];
            }

            $storagePath = storage_path('app/'.$imagePath);
            if (file_exists($storagePath)) {
                return ['path' => $storagePath, 'name' => 'Comprobante_'.$form->ci_number.'.jpg', 'is_temp' => false];
            }

            // Si es URL o S3
            $url = $form->invoice_view_url;
            if ($url && str_starts_with($url, 'http')) {
                return $this->downloadTempFile($url, 'Comprobante_'.$form->ci_number);
            }
        }

        // 2. Factura de venta Ecommerce (Dismac)
        $invoiceUrl = $form->invoice_url ?: ($form->sale?->invoice_url ?: null);
        if ($invoiceUrl && str_starts_with($invoiceUrl, 'http')) {
            return $this->downloadTempFile($invoiceUrl, 'Factura_Venta_'.$form->ci_number);
        }

        return null;
    }

    /**
     * Descarga temporalmente un archivo para adjuntarlo.
     */
    protected function downloadTempFile(string $url, string $prefix): ?array
    {
        try {
            $tempDir = storage_path('app/temp_mail');
            if (! File::isDirectory($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            $response = Http::timeout(10)->get($url);
            if ($response->successful()) {
                $ext = 'jpg';
                $contentType = $response->header('Content-Type');
                if (str_contains($contentType, 'pdf')) {
                    $ext = 'pdf';
                } elseif (str_contains($contentType, 'png')) {
                    $ext = 'png';
                }

                $filename = $prefix.'.'.$ext;
                $filePath = $tempDir.DIRECTORY_SEPARATOR.uniqid('att_').'_'.$filename;
                file_put_contents($filePath, $response->body());

                return ['path' => $filePath, 'name' => $filename, 'is_temp' => true];
            }
        } catch (\Exception $e) {
            Log::warning("No se pudo descargar el comprobante para adjuntar: {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Calcula el stock sobrante en formato de números súper corto.
     */
    public static function calculateStockSummary(): array
    {
        // Mayoristas
        $mayoristaBranch = Branch::where('channel', 'MAYORISTA')->first();
        $mStock = [100 => 0, 250 => 0, 500 => 0, 1000 => 0, 2000 => 0, 3000 => 0];
        if ($mayoristaBranch) {
            $pbs = PrizeBranch::with('prize')->where('branch_id', $mayoristaBranch->id)->get();
            foreach ($pbs as $pb) {
                $amt = (int) ($pb->prize?->amount ?? 0);
                if (isset($mStock[$amt])) {
                    $mStock[$amt] = (int) $pb->stock;
                }
            }
        }

        // Ecommerce
        $eStock = [100 => 0, 250 => 0, 500 => 0, 1000 => 0, 2000 => 0, 3000 => 0];
        $codes = Code::selectRaw('value, sum(case when status="available" and is_used=0 then 1 else 0 end) as available')
            ->groupBy('value')
            ->get();
        foreach ($codes as $c) {
            $amt = (int) $c->value;
            if (isset($eStock[$amt])) {
                $eStock[$amt] = (int) $c->available;
            }
        }

        $mLine = "100({$mStock[100]}) | 250({$mStock[250]}) | 500({$mStock[500]}) | 1000({$mStock[1000]}) | 2000({$mStock[2000]}) | 3000({$mStock[3000]})";
        $eLine = "100({$eStock[100]}) | 250({$eStock[250]}) | 500({$eStock[500]}) | 1000({$eStock[1000]}) | 2000({$eStock[2000]}) | 3000({$eStock[3000]})";

        return [
            'mayorista' => $mStock,
            'ecommerce' => $eStock,
            'mayorista_line' => $mLine,
            'ecommerce_line' => $eLine,
        ];
    }
}

