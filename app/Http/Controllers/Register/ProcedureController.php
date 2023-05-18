<?php

namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use App\Procedure;
use App\ProcedureFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProcedureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $procedure = Procedure::get();
        return view('register/procedure/list')->with(['data' => $procedure]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('register/procedure/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $path = null;
        if($request->file->isValid()){
            $path = $request->file->store('procedure');
        }
 
        $procedure = new Procedure();
        $procedure->name = $request->name;
        $procedure->link = $request->link;
        $procedure->file = $path;
        $procedure->save();
        return $procedure;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Procedure $procedure)
    {
        return view('register/procedure/view',compact('procedure'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Procedure $procedure)
    {
        return view('register/procedure/edit',compact('procedure'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Procedure $procedure)
    {
        
        if($request->hasFile('file')){
            $path = $request->file->store('procedure');
            $procedure->file = $path;
        }

        $procedure->name = $request->name;
        $procedure->link = $request->link;
               
        
        $procedure->save();
        return $procedure;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Procedure $procedure)
    {
        $procedure->delete();
        return $procedure;   
    }

    public function download(ProcedureFiles $procedureFiles)
    {
        if(Storage::exists($procedureFiles->file)){
            return Storage::download($procedureFiles->file);   
        }
        return 'Nenhum arquivo anexado';        
    }

    public function attachFile(Request $request,Procedure $procedure)
    {


        if ($request->hasFile('file')){

            $pathFile = $request->file->store('procedure');

            $procedureFile =  new ProcedureFiles();
            $procedureFile->procedure_id = $procedure->id;
            $procedureFile->name = $request->name;
            $procedureFile->file = $pathFile;
            $procedureFile->save();
            return response()->json('Arquivo anexado com sucesso');

        }
        return response()->json('Arquivo é um campo obrigatório',422);        
    }
    public function filesProcedure(Procedure $procedure){
        $files = ProcedureFiles::where('procedure_id', $procedure->id)->get();
        return response()->json($files);
    
    }

    public function deleteFilesProcedure(ProcedureFiles $procedureFiles){
        $procedureFiles->delete();
        return response()->json('Arquivo apagado com sucesso');
    }


}
