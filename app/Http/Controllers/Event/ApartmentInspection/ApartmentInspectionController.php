<?php

namespace App\Http\Controllers\Event\ApartmentInspection;

use App\ApartmentInspection;
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
       
        $apartmentInspection = ApartmentInspection::create($request->all());
        $items = json_decode($request->items);
        
        foreach($items as $item){
            $apartmentInspectionItem = new ApartmentInspectionItem();
            $apartmentInspectionItem->apartment_inspection_id = $apartmentInspection->id;
            $apartmentInspectionItem->appreciation = $item->appreciation;
            $apartmentInspectionItem->approved = $item->approved;
            $apartmentInspectionItem->ref = $item->ref;
            $apartmentInspectionItem->save();
        }

        return response('success');
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
}
