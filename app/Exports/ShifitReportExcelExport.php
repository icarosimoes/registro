<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;


class ShifitReportExcelExport implements FromView
{

    protected $shifit_report;
    protected $name;

    
    public function __construct($shifitReport,$name)
    {
        $this->shifit_report = $shifitReport;
        $this->name = $name;
        // $this->params = $params;
    }

    public function view(): View
    {
        $company = Company::first();
        $shifit_report = $this->shifit_report;
        $name = $this->name;
        // $params = $this->params;

        return view('event/shiftReport/export_excel', compact('shifit_report','company','name'));
        
    }
}
