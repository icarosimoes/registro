<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class WorkDiaryExport implements FromView
{

    protected $work_diary;
    protected $name;

    
    public function __construct($workDiary,$name)
    {
        $this->work_diary = $workDiary;
        $this->name = $name;
        // $this->params = $params;
    }

    public function view(): View
    {
        $company = Company::first();
        $work_diary = $this->work_diary;
        $name = $this->name;
        // $params = $this->params;

        return view('event.work_diary.export_excel', compact('work_diary','company','name'));

    }
}
