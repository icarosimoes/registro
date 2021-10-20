<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class InsactiveUserController extends BaseController
{
    var $errorPermission = false;
    
    public function index(){
        $this->errorPermission = true;
        return view('home')->with(['errorPermission' => $this->errorPermission]);
    }
}
