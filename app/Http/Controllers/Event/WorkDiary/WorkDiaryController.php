<?php

namespace App\Http\Controllers\Event\WorkDiary;

use App\Exports\WorkDiaryExport;
use App\Http\Controllers\Controller;
use App\WorkDiary;
use App\WorkDiaryActivity;
use App\WorkDiaryFrequencyAdm;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Excel;

class WorkDiaryController extends Controller
{
    public function index()
    {
        $workDiary = WorkDiary::orderBy('id','DESC');
        
        if(request()->date_start  && request()->date_end){
            $workDiary->whereBetween('date',[request()->date_start,request()->date_end]);
        }
        $workDiary = $workDiary->get();
        
        return view('event/work_diary/list', compact('workDiary'));
    }

    public function create()
    {

        $workDiary = false;

        if (request()->copy){
            $workDiary = WorkDiary::find(request()->copy);
        };
        return view('event/work_diary/create', compact('workDiary'));
    }

    public function store(Request $request)
    {

        $shift_time = json_decode($request->shift_time, true);
        $frequency_adm = json_decode($request->frequency_adm, true);
        $frequency_prod = json_decode($request->frequency_prod, true);
        $sub = json_decode($request->sub, true);
        $equipament = json_decode($request->equipament, true);
        $activity = json_decode($request->activity, true);
        $obs = json_decode($request->obs, true);

        DB::beginTransaction();

        $workDiary  = new WorkDiary();
        $workDiary->date = now();
        $workDiary->save();


        //salva as turno
        foreach ($shift_time as $item) {
            $workDiary->work_diary_shift_time()->create($item);
        }

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

        //salvar equipamentos 
        foreach ($equipament as $item) {
            $workDiary->work_diary_equipament()->create($item);
        }

        //salvar atividades
        foreach ($activity as $key => $item) {

            //salva o arquivo anexado
            if ($request['activity_attachment-' . $key] != 'undefined') {
                $item['attachment'] =  Storage::put('files_work_diary', $request['activity_attachment-' . $key]);
            }

            $workDiary->work_diary_activity()->create($item);
        }

        //salvar obs
        foreach ($obs as $item) {
            $workDiary->work_diary_obs()->create($item);
        }

        DB::commit();
    }

    public function show(WorkDiary $workDiary)
    {
        return view('event/work_diary/view', compact('workDiary'));
    }

    public function edit(WorkDiary $workDiary)
    {
        return view('event/work_diary/edit', compact('workDiary'));
    }

    public function update(Request $request, WorkDiary $workDiary)
    {
        $shift_time = json_decode($request->shift_time, true);
        $frequency_adm = json_decode($request->frequency_adm, true);
        $frequency_prod = json_decode($request->frequency_prod, true);
        $sub = json_decode($request->sub, true);
        $equipament = json_decode($request->equipament, true);
        $activity = json_decode($request->activity, true);
        $obs = json_decode($request->obs, true);

        DB::beginTransaction();

        //$workDiary  = new WorkDiary();
        //$workDiary->date = now();
        //$workDiary->save();

        //salva as turno\tempo
        $workDiary->work_diary_shift_time()->delete();
        foreach ($shift_time as $item) {
            $workDiary->work_diary_shift_time()->create($item);
        }

        //salva as frequencia adm
        $workDiary->work_diary_frequency_adm()->delete();
        foreach ($frequency_adm as $item) {
            $workDiary->work_diary_frequency_adm()->create($item);
        }


        //salva as frequencia prod
        $workDiary->work_diary_frequency_prod()->delete();
        foreach ($frequency_prod as $item) {
            $workDiary->work_diary_frequency_prod()->create($item);
        }

        //salva as sub- empreiteiras
        $workDiary->work_diary_sub()->delete();
        foreach ($sub as $item) {
            $workDiary->work_diary_sub()->create($item);
        }

        //salvar equipamentos 
        $workDiary->work_diary_equipament()->delete();
        foreach ($equipament as $item) {
            $workDiary->work_diary_equipament()->create($item);
        }

        
        //salvar atividades
        $delete_ids = [];
        
        foreach ($activity as $key => $item) {
            
            if ($item['id'] != '') { //update

                //salva o arquivo anexado
                if ($request['activity_attachment-' . $key] != 'undefined') {
                    $item['attachment'] =  Storage::put('files_work_diary', $request['activity_attachment-' . $key]);
                } else {
                    $item['attachment'] = null;
                }

                $workDiaryActivity = WorkDiaryActivity::find($item['id']);
                $workDiaryActivity->sector = $item['sector']; 
                $workDiaryActivity->team = $item['team']; 
                $workDiaryActivity->description = $item['description']; 
                $workDiaryActivity->occurrence_id = $item['occurrence_id']; 

                if($item['attachment']){
                    $workDiaryActivity->attachment = $item['attachment']; 
                }
                
                $workDiaryActivity->save();
                array_push($delete_ids,$workDiaryActivity->id);

            } else { //store
                
                //salva o arquivo anexado
                if ($request['activity_attachment-' . $key] != 'undefined') {
                    $item['attachment'] =  Storage::put('files_work_diary', $request['activity_attachment-' . $key]);
                } 
                //dd($item);
               $workDiaryActivity = $workDiary->work_diary_activity()->create($item);
               array_push($delete_ids,$workDiaryActivity->id);
            }
        }
        $workDiary->work_diary_activity()->whereNotIn('id',$delete_ids)->delete();
        

        //salvar obs
        $workDiary->work_diary_obs()->delete();
        foreach ($obs as $item) {
            $workDiary->work_diary_obs()->create($item);
        }

        DB::commit();
    }

    public function destroy(WorkDiary $workDiary)
    {
        $workDiary->delete();
    }

    public function downloadActivity(WorkDiaryActivity $id)
    {
        return Storage::download($id->attachment);
    }

    public function exportPdf($id,$name){
        
        if(!$name){
            $name = "Indefinido";
        }
        $workDiary = WorkDiary::find($id);       
        return Excel::download(new WorkDiaryExport($workDiary, $name), 'relatorio.xlsx');
        // $data = session()->get('data');
        // if (session()->get('params')) {
        //     $params = session()->get('params');
        // }else{
        //     $params = false;
        // }

        // $pdf = PDF::loadView('occurrence/export_pdf',compact(['data', 'name']))->setPaper('a4', 'landscape');
        // return $pdf->stream('relatorio.pdf'); 
    }
}
