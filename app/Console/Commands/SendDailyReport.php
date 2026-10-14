<?php

namespace App\Console\Commands;

use App\Mail\DailyReportMail;
use App\Services\DailyReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class SendDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily {--date= : Fecha específica para el reporte (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía el reporte diario de formularios Whirlpool por email con los registros y stock a la fecha';

    /**
     * Execute the console command.
     */
    public function handle(DailyReportService $service)
    {
        $this->info('Iniciando generación de reporte diario de formularios...');

        // Fecha de reporte (por defecto el día anterior)
        $date = $this->option('date') ?: now()->subDay()->toDateString();
        $dateStart = $date;
        $dateEnd = $date;

        $this->info("Fecha (día anterior): {$date}");

        // Obtener datos de resumen para el cuerpo del correo
        $data = $service->getSummaryData($dateStart, $dateEnd);

        // Asegurar que el directorio de almacenamiento temporal exista
        $directory = storage_path('app/public');
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Generar Excel con las 3 pestañas:
        // 1. Registros del día anterior
        // 2. Total a la fecha
        // 3. Stock a la fecha
        $fileName = 'Reporte_Diario_'.$date.'.xlsx';
        $tempPath = $directory.DIRECTORY_SEPARATOR.$fileName;

        $this->info('Generando archivo Excel con las 3 pestañas...');
        $service->exportDailyReportToPath($tempPath, $date);

        $this->info("Excel generado temporalmente en: {$tempPath}");

        // Obtener destinatarios de la configuración
        $recipients = config('weekly_report.recipients_daily');

        if (empty($recipients)) {
            $this->error('No hay destinatarios configurados en weekly_report.recipients_daily');

            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            return 1;
        }

        $this->info('Enviando emails a: '.implode(', ', $recipients));

        // Enviar email reutilizando el template prediseñado con el archivo adjunto
        Mail::to($recipients)->send(new DailyReportMail($dateStart, $dateEnd, $data, $tempPath));

        // Eliminar archivo temporal
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        $this->info('Reporte diario enviado exitosamente.');

        return 0;
    }
}
