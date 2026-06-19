<?php

namespace App\Http\Controllers\Event\ApartmentInspection_v2;

use App\ApartamentInspectionItemAttach;
use App\ApartmentInpectionItemAttach;
use App\ApartmentInspection;
use App\ApartmentInspectionAttach;
use App\ApartmentInspectionItem;
use App\ApartmentInspectionItems_v2;
use App\ApartmentInspectionsV2;
use App\ApartmentInspectionTypes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApartmentInspectionController extends Controller
{

  function index(Request $request)
  {
    $apartmentInspection = ApartmentInspectionsV2::orderBy('inspection_date', 'DESC');

    if ($request->date_start && $request->date_end) {
      $apartmentInspection->whereBetween('inspection_date', [$request->date_start, $request->date_end]);
    }
    $apartmentInspection = $apartmentInspection->paginate(20);
    return view('event.apartament_inspection_v2.list', compact('apartmentInspection'));
  }

  function create()
  {

    return view('event.apartament_inspection_v2.create');
  }

  function loadApartmentInspections(Request $request)
  {
    if($request->type_unit==''){
      $apartamentInspectionType = ApartmentInspectionTypes::first();
    }else{
      $apartamentInspectionType = ApartmentInspectionTypes::find($request->type_unit);
    }

    if($apartamentInspectionType){
    $apartmentInspections = ApartmentInspectionsV2::where('type_unit', $apartamentInspectionType->id)->latest()->first();
    if(!$apartmentInspections){
      $apartmentInspections = new ApartmentInspectionsV2();
      $apartmentInspections->items = [];
    }else{
      $apartmentInspections->items = $apartmentInspections->apartmentInspectionItems->groupBy('group');
      unset($apartmentInspections->apartmentInspectionItems);
    }
    }else{
      $apartmentInspections = new ApartmentInspectionsV2();
      $apartmentInspections->items = [];
    }

    
   
    return response()->json($apartmentInspections);
  }

  /**
   * SALVA NOVO TIPO DE UNIDADE 
   */
  public function saveTypeUnit(Request $request)
  {
    $type_unit = $request->input('new_type_unit');
    if ($type_unit) {
      // Salva o novo tipo de unidade no banco de dados
      $newTypeUnit = new ApartmentInspectionTypes();
      $newTypeUnit->name = $type_unit;
      $newTypeUnit->save();

      $apartmentInspectionTypes = ApartmentInspectionTypes::get();
      $response = [
        'types' => $apartmentInspectionTypes,
        'new_type' => $newTypeUnit,

      ];
      return response()->json($response);
    }
  }

  public function loadTypesUnit()
  {
    $apartmentInspectionTypes = ApartmentInspectionTypes::get();
    return response()->json($apartmentInspectionTypes);
  }


  function store(Request $request)
  {
    DB::beginTransaction();
    // organiza os anexos em arrays por item
    // $attachs = $this->organizeAttachs($request);

    $apartmentInspection = ApartmentInspectionsV2::create($request->all());

    $groups = json_decode($request->items);
    // dd($items);
    foreach ($groups as $group) {

      foreach ($group as $index => $item) {

        if ($item->occurrence_id == '' || $item->occurrence_id == null) {
          $occurrence_id = null;
        } else {
          $occurrence_id = $item->occurrence_id;
        }

        $apartmentInspectionItem = new ApartmentInspectionItems_v2();
        $apartmentInspectionItem->apartment_inspection_id = $apartmentInspection->id;
        $apartmentInspectionItem->group = $item->group;
        $apartmentInspectionItem->service = $item->service;
        $apartmentInspectionItem->item_verification = $item->item_verification;
        $apartmentInspectionItem->appreciation = $item->appreciation;
        $apartmentInspectionItem->approved = $item->approved;
        $apartmentInspectionItem->occurrence_id = $occurrence_id;
        $apartmentInspectionItem->save();
      }
      //salva os anexos
      //verifica se tem anexos para o item e salva
         if (isset($request[$item->group.'-'.$index])) {
          $file = $request[$item->group.'-'.$index];
          $path = $file->store('anexo_apartment_inspection');
          // Storage::put('anexo_apartment_inspection/', $file);        
          $attach = new ApartamentInspectionItemAttach();
          $attach->apartment_item_id = $apartmentInspectionItem->id;
          $attach->name = $file->getClientOriginalName();
          $attach->attach = $path;
          $attach->save();

         }
    }
    DB::commit();

    return response('success');
  }

  function organizeAttachs($request)
  {
    $attachs_names = json_decode($request->names_attachs, true);
    $names_attachs = [];
    foreach ($attachs_names as $item) {
      $names_attachs[array_key_first($item)] = reset($item);
    }
    $attachs = [];

    foreach ($request->all() as $key => $item) {

      //identifiquei oq é anexo
      if (substr($key, 0, 7) == 'attachs') {

        $ref =  explode('_', $key)[2]; //substr($key, -4);

        if (!isset($attachs[$ref])) {
          $attachs[$ref] = [];
        }
        array_push($attachs[$ref], ['file' => $item, 'name' => $names_attachs[$key]]);
      }
    }
    return $attachs;
  }

  function show(ApartmentInspectionsV2 $apartment_inspection_v2)
  {
    return view('event.apartament_inspection_v2.show', compact('apartment_inspection'));
  }

  function edit(ApartmentInspectionsV2 $apartment_inspection_v2)
  {
    $apartment_inspection = $apartment_inspection_v2;
    return view('event.apartament_inspection_v2.edit', compact('apartment_inspection'));
  }

  /**
   * carrega os dados de uma inspeção para edição
   */
  function getApartmentInspection(ApartmentInspectionsV2 $apartment_inspection)
  {
    
    $apartment_inspection->load('apartmentInspectionItems.atachments');
    $apartment_inspection->items = $apartment_inspection->apartmentInspectionItems->groupBy('group');
    unset($apartment_inspection->apartmentInspectionItems);
    return response()->json($apartment_inspection);
  }

  function update(Request $request, ApartmentInspectionsV2 $apartment_inspection_v2)
  {
    DB::beginTransaction();
    $apartment_inspection = $apartment_inspection_v2;
    $apartment_inspection->update($request->all());
    $groups = json_decode($request->items);

    foreach ($groups as $group) {
      $ids = collect($group)->pluck('id');
      
      ApartmentInspectionItems_v2::where('apartment_inspection_id', $apartment_inspection->id)
        ->where('group', $group[0]->group)
        ->whereNotIn('id', $ids)
        ->delete();
        
      foreach ($group as $index => $item) {
         
        if ($item->occurrence_id == '' || $item->occurrence_id == null) {
          $occurrence_id = null;
        } else {
          $occurrence_id = $item->occurrence_id;
        }

        $apartmentInspectionItem = ApartmentInspectionItems_v2::updateOrCreate(
          ['id' => @$item->id],
          [
            'apartment_inspection_id' => $apartment_inspection->id,
            'group' => $item->group,
            'service' => $item->service,
            'item_verification' => $item->item_verification,
            'appreciation' => $item->appreciation,
            'approved' => $item->approved,
            'occurrence_id' => $occurrence_id
          ]
        );
        //verifica se tem anexos para o item e salva
         if (isset($request[$item->group.'-'.$index])) {
          $file = $request[$item->group.'-'.$index];
          $path = $file->store('anexo_apartment_inspection');
          $attach = new ApartamentInspectionItemAttach();
          $attach->apartment_item_id = $apartmentInspectionItem->id;
          $attach->name = $file->getClientOriginalName();
          $attach->attach = $path;
          $attach->save();

         }
        


      }
             
    }
   
    DB::commit();
    return response('success');
  }

  function destroy(ApartmentInspection $apartment_inspection)
  {
    $apartment_inspection->delete();
    return response('deleted');
  }

  function loadAttach(ApartmentInspection $apartment_inspection)
  {
    $apartmentInspectionAttach = ApartmentInspectionAttach::where('apartment_inspection_id', $apartment_inspection->id)
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
  function attach(Request $request, ApartmentInspection $apartment_inspection)
  {

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
    $apartmentInspectionAttach = ApartmentInspectionAttach::where('apartment_inspection_id', $apartment_inspection->id)
      ->get();

    return response()->json($apartmentInspectionAttach);
  }

  function deleteAttach(ApartmentInspectionAttach $apartment_inspection_attach)
  {
    $apartment_inspection_attach->delete();
    //carrega os anexos
    $apartmentInspectionAttach = ApartmentInspectionAttach::where('apartment_inspection_id', $apartment_inspection_attach->apartment_inspection_id)
      ->get();
    return response()->json($apartmentInspectionAttach);
  }

  // attach items

  function loadItemsAttach(ApartmentInspectionItem $apartment_inspection_item)
  {
    $apartmentInpectionItemAttach = ApartmentInpectionItemAttach::where('apartment_item_id', $apartment_inspection_item->id)->get();
    return response()->json($apartmentInpectionItemAttach);
  }

  function itemAttach(Request $request, ApartmentInspectionItem $apartment_inspection_item)
  {
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
    $apartmentInspectionItemsAttach = ApartmentInpectionItemAttach::where('apartment_item_id', $apartment_inspection_item->id)
      ->get();

    return response()->json($apartmentInspectionItemsAttach);
  }

  // download item atacch
  function downloadItemAttach( $id)
  {
    $apartment_inspection_item_attach = ApartamentInspectionItemAttach::find($id);
    // Caminho do arquivo no storage
    $filePath = storage_path('app/' . $apartment_inspection_item_attach->attach);

    if (!file_exists($filePath)) {
      abort(404, 'Arquivo não encontrado.');
    }

    return response()->download($filePath);
  }

  //delete item attach
  function deleteItemAttach(ApartmentInpectionItemAttach $apartment_inspection_item_attach)
  {
    $apartment_inspection_item_attach->delete();
    $apartmentInspectionItemsAttach = ApartmentInpectionItemAttach::where('apartment_item_id', $apartment_inspection_item_attach->apartment_item_id)
      ->get();

    return response()->json($apartmentInspectionItemsAttach);
  }
}
