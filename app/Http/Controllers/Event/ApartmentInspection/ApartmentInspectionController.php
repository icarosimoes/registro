<?php

namespace App\Http\Controllers\Event\ApartmentInspection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApartmentInspectionController extends Controller
{

    function index() {
        $data = [];
        return view('event.apartament_inspection.list',compact('data'));
    }
    function create() {
        return view('event.apartament_inspection.create');
    }
    
    function store() {}
    function edit() {}
    function update() {}
    function destroy() {}
}
