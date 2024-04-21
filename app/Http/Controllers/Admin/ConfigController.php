<?php

namespace App\Http\Controllers\Admin;

use App\ConfigForm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index(){
        $forms = ConfigForm::get();
        return view('modules.admin.config.index',compact('forms'));         
    }

    public function updateConfigForm(ConfigForm $ConfigForm, Request $request){
        $ConfigForm->active = $request->active;
        $ConfigForm->save(); 
        return  'success';  
    }
}
