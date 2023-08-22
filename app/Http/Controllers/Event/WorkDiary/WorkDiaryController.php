<?php

namespace App\Http\Controllers\Event\WorkDiary;

use App\Http\Controllers\Controller;
use App\WorkDiary;
use App\WorkDiaryFrequencyAdm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkDiaryController extends Controller
{
    public function index()
    {
        $data = [];
        return view('event/work_diary/list', compact('data'));
    }

    public function create()
    {

        $data = [];
        return view('event/work_diary/create', compact('data'));
    }

    public function store(Request $request)
    {

        $frequency_adm = json_decode($request->frequency_adm, true);
        $frequency_prod = json_decode($request->frequency_prod, true);
        $sub = json_decode($request->sub, true);

        DB::beginTransaction();

        $workDiary  = new WorkDiary();
        $workDiary->date = now();
        $workDiary->save();

        //salva as frequencia adm
        foreach ($frequency_adm as $item) {
            $workDiary->work_diary_frequency_adm()->create($item);
        }

        //salva as frequencia prod
        foreach ($frequency_prod as $item) {
            $workDiary->work_diary_frequency_prod()->create($item);
        }
        
        //salva as sub- empreiteiras
        foreach ($sub as $item) {
            $workDiary->work_diary_sub()->create($item);
        }





        DB::commit();
    }

    public function show()
    {
    }

    public function edit()
    {
    }

    public function update()
    {
    }

    public function destroy()
    {
    }
}
