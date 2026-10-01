<?php

namespace App\Console\Commands;

use App\Mail\WeeklyReportMail;
use App\Models\Participant;
use App\Models\ParticipantCoupon;
use App\Models\Sale;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;

class SendWeeklyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-weekly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly report of participants, coupons and sales via email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting weekly report generation...');

        $startStr = config('weekly_report.range.start', 'last week sunday');
        $endStr = config('weekly_report.range.end', 'this week sunday');

        $start = Carbon::parse($startStr)->setTime(8, 0);
        $end = Carbon::parse($endStr)->setTime(8, 0);

        $this->info("Range: {$start->format('Y-m-d')} to {$end->format('Y-m-d')}");

        $totals = [
            'participants' => Participant::whereBetween('created_at', [$start, $end])->count(),
            'valid_participants' => Participant::whereIn('id', Sale::whereBetween('created_at', [$start, $end])->whereHas('valid_sale_items')->select('participant_id'))->count(),
            'coupons' => ParticipantCoupon::whereBetween('created_at', [$start, $end])->count(),
            'sales' => Sale::whereBetween('created_at', [$start, $end])->whereHas('valid_sale_items')->count(),
        ];

        $participants = Participant::whereIn('id', Sale::whereBetween('created_at', [$start, $end])->whereHas('valid_sale_items')->select('participant_id'))
            ->get()->map(function ($p) {
                return [
                    'ID' => $p->id,
                    'Nombre' => $p->name,
                    'CI' => $p->ci_number,
                    'Celular' => $p->cellphone,
                    'Ciudad' => $p->city,
                    'Registrado' => $p->created_at->format('Y-m-d H:i:s'),
                ];
            });

        $coupons = ParticipantCoupon::with(['participant', 'sale'])
            ->whereBetween('created_at', [$start, $end])->get()->map(function ($c) {
                return [
                    'Código' => $c->code,
                    'Participante' => $c->participant?->name,
                    'Factura' => $c->sale?->invoice_number,
                    'UDN' => $c->business_unit,
                    'Registrado' => $c->created_at->format('Y-m-d H:i:s'),
                ];
            });

        $sales = Sale::with('participant')->whereBetween('created_at', [$start, $end])->whereHas('valid_sale_items')->get()->map(function ($s) {
            return [
                'Nombre' => $s->name,
                'CI' => $s->participant?->ci_number,
                'Teléfono' => $s->participant?->cellphone,
                'Tipo Venta' => $s->sale_type,
                'Total' => $s->total_amount,
                'Items' => $s->quantity_items,
                'Factura N°' => $s->invoice_number,
                'Agencia' => $s->city_agency,
                'Código Control' => $s->control_code,
                'URL Factura' => $s->invoice_url,
                'Registrado' => $s->created_at->format('Y-m-d H:i:s'),
                'Enlace Directo' => url('/admin/sales?tableAction=edit&tableActionRecord='.$s->id),
            ];
        });

        $sheets = new SheetCollection([
            'participantes' => $participants,
            'cupones' => $coupons,
            'ventas' => $sales,
        ]);

        $directory = storage_path('app/reports');
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $timestamp = now()->format('Ymd_His');
        $path = $directory.'/Reporte_Dismac_'.$timestamp.'.xlsx';

        (new FastExcel($sheets))->export($path);

        $this->info("Excel file generated at: {$path}");

        // Send email
        $recipients = config('weekly_report.recipients', []);

        if (empty($recipients)) {
            $this->warn('No recipients configured in config/weekly_report.php');

            return;
        }

        $paths = [$path];

        try {
            Mail::to($recipients)->send(new WeeklyReportMail($start, $end, $totals, $paths));
            $this->info('Email sent successfully!');
        } catch (\Exception $e) {
            Log::error('Error sending weekly report: '.$e->getMessage());
            $this->error('Failed to send email. Check logs.');
        }

        // Clean up the file after sending if needed, or keep it.
        // File::delete($path);
    }
}
