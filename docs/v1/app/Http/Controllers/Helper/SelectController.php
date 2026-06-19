<?php

namespace App\Http\Controllers\Helper;

use App\Func;
use App\Http\Controllers\Controller;
use App\Local;
use App\Models\Occurrence;
use App\Models\User;
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
    
    function getOccurrences(Request $request){
        $occurrences = Occurrence::select('id','title')
        ->selectSearch($request)
        ->paginate(100);

        $occurrences->map(function($item){
            return $item->text = @$item->id .' - '.@$item->title;
        });

        return  $occurrences;
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
    
    function getFunctions(Request $request){
        $functions = Func::select('id','name')
        ->selectSearch($request)
        ->paginate(100);

        $functions->map(function($item){
            return $item->text = @$item->id .' - '.@$item->name;
        });

        return  $functions;
    }

    function getUsers(Request $request){
        $users = User::select('id','name')
        ->selectSearch($request)
        ->paginate(100);
        
        $users->map(function($item){
            return $item->text = @$item->id .' - '.@$item->name;
        });

        return  $users;
    }
}
