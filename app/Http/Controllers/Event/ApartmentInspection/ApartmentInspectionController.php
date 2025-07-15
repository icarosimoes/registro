<?php

namespace App\Http\Controllers\Event\ApartmentInspection;

use App\ApartmentInpectionItemAttach;
use App\ApartmentInspection;
use App\ApartmentInspectionAttach;
use App\ApartmentInspectionItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApartmentInspectionController extends Controller
{

    function index(Request $request) {
        
        $apartmentInspection = ApartmentInspection::orderBy('inspection_date','DESC');

        if($request->date_start && $request->date_end){
            $apartmentInspection->whereBetween('inspection_date',[$request->date_start , $request->date_end]);
        }
        $apartmentInspection = $apartmentInspection->get();
        return view('event.apartament_inspection.list',compact('apartmentInspection'));
    }
    
    function create() {
        return view('event.apartament_inspection.create');
    }
    
    function store(Request $request) {
       
        // organiza os anexos em arrays por item
        $attachs = $this->organizeAttachs($request);
        $apartmentInspection = ApartmentInspection::create($request->all());
        $items = json_decode($request->items);
       
        foreach($items as $item){
            $apartmentInspectionItem = new ApartmentInspectionItem();
            $apartmentInspectionItem->apartment_inspection_id = $apartmentInspection->id;
            $apartmentInspectionItem->appreciation = $item->appreciation;
            $apartmentInspectionItem->approved = $item->approved;
            $apartmentInspectionItem->ref = $item->ref;
            $apartmentInspectionItem->save();

            //salva os anexos
            if(isset($attachs[$item->ref])){
       
                foreach($attachs[$item->ref] as $attach){
                    $file =$attach['file'];
                    $name = $attach['name'];
                    $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('anexo_apartment_inspection', $filename);
                    // Salvar registro no banco
                    $attach = new ApartmentInpectionItemAttach();
                    $attach->apartment_item_id = $apartmentInspectionItem->id;
                    $attach->name = $name;
                    $attach->attach = $path;
                    $attach->save();
                }
            }
        }

        return response('success');
    }

    function organizeAttachs($request){
        $attachs_names = json_decode($request->names_attachs,true);
        $names_attachs = [];    
        foreach( $attachs_names as $item){
                    $names_attachs[array_key_first($item)] = reset($item);
        }
        $attachs = [];

        foreach ($request->all() as $key => $item){
               
            //identifiquei oq é anexo
            if( substr($key,0,7) == 'attachs'){
                $ref = substr($key,-3);
                
                if(!isset($attachs[$ref])){
                    $attachs[$ref] = [];
                }
                array_push($attachs[$ref],['file'=>$item, 'name'=>$names_attachs[$key]]);
            }
        }
        return $attachs;
    }

    function show(ApartmentInspection $apartment_inspection){
       return view('event.apartament_inspection.show',compact('apartment_inspection')); 
    }
    function edit(ApartmentInspection $apartment_inspection) {
       return view('event.apartament_inspection.edit',compact('apartment_inspection'));
    }

    function update(Request $request, ApartmentInspection $apartment_inspection) {
           
        // unset($request['_method']);
        $apartment_inspection->update($request->all());
        $items = json_decode($request->items);
        
        foreach($items as $item){
            $apartmentInspectionItem = ApartmentInspectionItem::where('apartment_inspection_id',$apartment_inspection->id)
            ->where('ref',$item->ref)
            ->first();
             
            $apartmentInspectionItem->apartment_inspection_id = $apartment_inspection->id;
            $apartmentInspectionItem->appreciation = $item->appreciation;
            $apartmentInspectionItem->approved = $item->approved;
            $apartmentInspectionItem->ref = $item->ref;
            $apartmentInspectionItem->save();
        }
        return response('success');
    }
    function destroy(ApartmentInspection $apartment_inspection) {
        $apartment_inspection->delete();
        return response('deleted');
    }

    function loadAttach(ApartmentInspection $apartment_inspection){
        $apartmentInspectionAttach = ApartmentInspectionAttach::where('apartment_inspection_id',$apartment_inspection->id)
        ->get();     
        return response()->json($apartmentInspectionAttach);
    }
    
    function downloadAttach(ApartmentInspectionAttach $apartment_inspection_attach)
    {
        // Caminho do arquivo no storage
        $filePath = storage_path('app/' . $apartment_inspection_attach->attach);

        if (!file_exists($filePath)) {
            abort(404, 'Arquivo não encontrado.');
        }

        // Nome do arquivo para download
        // $downloadName = $apartment_inspection_attach->name ?? basename($filePath);

        return response()->download($filePath);
    }
    
    //anexa arquivos 
    function attach(Request $request,ApartmentInspection $apartment_inspection){

        // anexar arquivo

        // Validação básica
        // $request->validate([
        //     'file' => 'required|file|max:10240', // 10MB
        //     'name' => 'nullable|string|max:255',
        // ]);

        // Salvar arquivo
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('anexo_apartment_inspection', $filename);
        } else {
            return response()->json(['error' => 'Arquivo não enviado.'], 400);
        }

        // Salvar registro no banco
        $attach = new ApartmentInspectionAttach();
        $attach->apartment_inspection_id = $apartment_inspection->id;
        $attach->name = $request->input('name');
        $attach->attach = $path;
        $attach->save();

        //carrega os anexos
        $apartmentInspectionAttach = ApartmentInspectionAttach::where('apartment_inspection_id',$apartment_inspection->id)
        ->get();

        return response()->json($apartmentInspectionAttach);

    }

    function deleteAttach(ApartmentInspectionAttach $apartment_inspection_attach){
        $apartment_inspection_attach->delete();
        //carrega os anexos
        $apartmentInspectionAttach = ApartmentInspectionAttach::where('apartment_inspection_id',$apartment_inspection_attach->apartment_inspection_id)
        ->get();
        return response()->json($apartmentInspectionAttach);
    }

    // attach items

    function loadItemsAttach(ApartmentInspectionItem $apartment_inspection_item){
        $apartmentInpectionItemAttach = ApartmentInpectionItemAttach::where('apartment_item_id', $apartment_inspection_item->id)->get();
        return response()->json($apartmentInpectionItemAttach);
    }

    function itemAttach (Request $request,ApartmentInspectionItem $apartment_inspection_item){
        // Salvar arquivo
        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('anexo_apartment_inspection', $filename);
        } else {
            return response()->json(['error' => 'Arquivo não enviado.'], 400);
        }

        // Salvar registro no banco
        $attach = new ApartmentInpectionItemAttach();
        $attach->apartment_item_id = $apartment_inspection_item->id;
        $attach->name = $request->input('name');
        $attach->attach = $path;
        $attach->save();

        //carrega os anexos
        $apartmentInspectionItemsAttach = ApartmentInpectionItemAttach::where('apartment_item_id',$apartment_inspection_item->id)
        ->get();

        return response()->json($apartmentInspectionItemsAttach);
    }

    // download item atacch
    function downloadItemAttach(ApartmentInpectionItemAttach $apartment_inspection_item_attach){
    
        // Caminho do arquivo no storage
    $filePath = storage_path('app/' . $apartment_inspection_item_attach->attach);

    if (!file_exists($filePath)) {
        abort(404, 'Arquivo não encontrado.');
    }
    
    return response()->download($filePath);        
    }

    //delete item attach
    function deleteItemAttach(ApartmentInpectionItemAttach $apartment_inspection_item_attach){
        $apartment_inspection_item_attach->delete();
        $apartmentInspectionItemsAttach = ApartmentInpectionItemAttach::where('apartment_item_id',$apartment_inspection_item_attach->apartment_item_id)
        ->get();

        return response()->json($apartmentInspectionItemsAttach);
    }
}
