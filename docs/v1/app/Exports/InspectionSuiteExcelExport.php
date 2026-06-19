<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;


class InspectionSuiteExcelExport implements FromView
{
    protected $inspectionSuite;
    protected $name;

    
    public function __construct($inspectionSuite,$name)
    {
        $this->inspectionSuite = $inspectionSuite;
        $this->name = $name;
        // $this->params = $params;
    }

    public function view(): View
    {
        $company = Company::first();
        $inspectionSuite = $this->inspectionSuite;
        $name = $this->name;
        // $params = $this->params;

        return view('event.inspection_suites.export_excel', compact('inspectionSuite','company','name'));

    }
}
