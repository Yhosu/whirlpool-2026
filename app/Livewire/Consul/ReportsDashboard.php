<?php

namespace App\Livewire\Consul;

use App\Services\ConsulReportService;
use Livewire\Component;
use OpenSpout\Common\Entity\Style\Style;
use Rap2hpoutre\FastExcel\FastExcel;

class ReportsDashboard extends Component
{
    public $dateStart;

    public $dateEnd;

    public function mount()
    {
        $this->dateStart = now()->toDateString();
        $this->dateEnd = now()->toDateString();
    }

    public function exportReport(ConsulReportService $service)
    {
        $sheets = $service->generateExcelSheets($this->dateStart, $this->dateEnd);

        $headerStyle = (new Style)
            ->setFontBold()
            ->setBackgroundColor('E2E8F0');

        return (new FastExcel($sheets))
            ->headerStyle($headerStyle)
            ->download('Reporte_Consul_'.now()->format('Ymd_His').'.xlsx');
    }

    public function render(ConsulReportService $service)
    {
        $data = $service->getSummaryData($this->dateStart, $this->dateEnd);

        return view('livewire.consul.reports-dashboard', $data);
    }
}
