<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Controller;
use App\Local;
use App\Sector;
use Illuminate\Http\Request;

class SelectController extends Controller
{
    function getLocals(Request $request){
        $locals = Local::select('id','name')
        ->selectSearch($request)
        ->paginate(100);

        $locals->map(function($item){
            return $item->text = @$item->id .' - '.@$item->name;
        });

        return  $locals;
    }
    
    function getSectors(Request $request){
        $sectors = Sector::select('id','name')
        ->selectSearch($request)
        ->paginate(100);

        $sectors->map(function($item){
            return $item->text = @$item->id .' - '.@$item->name;
        });

        return  $sectors;
    }
}
