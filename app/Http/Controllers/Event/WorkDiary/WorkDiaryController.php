<?php

namespace App\Http\Controllers\Event\WorkDiary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkDiaryController extends Controller
{
    public function index(){
        $data = [];                
        return view('event/work_diary/list',compact('data'));
    }
    
    public function create(){
        
        $data = [];
        return view('event/work_diary/create',compact('data'));
    }

    public function store(){

        
    }
    
    public function show(){


    }
    
    public function edit(){

        
    }
    
    public function update(){


    }
    
    public function destroy(){


    }
    

}
