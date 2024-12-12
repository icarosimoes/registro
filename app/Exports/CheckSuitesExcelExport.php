<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class CheckSuitesExcelExport implements FromView
{
  protected $checkSuites;
  protected $name;


  public function __construct($checkSuites, $name)
  {
    $this->checkSuites = $checkSuites;
    $this->name = $name;
    // $this->params = $params;
  }

  public function view(): View
  {
    $company = Company::first();
    $checkSuites = $this->checkSuites;
    $name = $this->name;
    

    return view('event.check_suites.export_excel', compact('checkSuites', 'company', 'name'));
  }
}
