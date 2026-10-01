<?php

namespace App\Console\Commands;

use App\Mail\DailyConsulReportMail;
use App\Services\ConsulReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyConsulReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily-consul';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía el reporte diario de gestión Consul por email';

    /**
     * Execute the console command.
     */
    public function handle(ConsulReportService $service)
    {
        $this->info('Iniciando generación de reporte diario Consul...');

        // Fecha de ayer
        $yesterday = now()->subDay()->toDateString();
        $dateStart = $yesterday;
        $dateEnd = $yesterday;

        $this->info("Rango de fechas: {$dateStart} - {$dateEnd}");

        // Obtener datos de resumen
        $data = $service->getSummaryData($dateStart, $dateEnd);

        // Generar Excel temporal
        $fileName = 'Reporte_Consul_'.$dateStart.'.xlsx';
        $tempPath = storage_path('app/public/'.$fileName);

        $service->exportToPath($tempPath, $dateStart, $dateEnd);

        // Obtener destinatarios de la configuración
        $recipients = config('weekly_report.recipients_consul', ['josuandroidg7@gmail.com']);

        if (empty($recipients)) {
            $this->error('No hay destinatarios configurados en weekly_report.recipients_consul');

            return 1;
        }

        $this->info('Enviando emails a: '.implode(', ', $recipients));

        // Enviar email
        Mail::to($recipients)->send(new DailyConsulReportMail($dateStart, $dateEnd, $data, $tempPath));

        // Eliminar archivo temporal
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        $this->info('Reporte diario enviado exitosamente.');

        return 0;
    }
}
