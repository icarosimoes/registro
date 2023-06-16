<?php

namespace App\Http\Controllers\Event\CheckSuites;

use App\CheckSuite;
use App\CheckSuiteItem;
use App\Http\Controllers\Controller;
use App\Local;
use App\Models\User;
use CreateCheckSuiteItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckSuitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = $request->all();

        $checkSuites = CheckSuite::orderBy('id','DESC');

        if(isset($request->local)){
            $checkSuites->where('local_id',$request->local);    
            $filter['local']= Local::find($request->local);
        }

        if(isset($request->user)){
            $checkSuites->where('user_id',$request->user); 
            $filter['user']= User::find($request->user);   
        }
        
        if(isset($request->date_start ) && $request->date_start != null){
            $checkSuites->where('date','>=',$request->date_start);    
        }

        if(isset($request->date_end) && $request->date_end != null ){
            $checkSuites->where('date','<=',$request->date_end .' 23:59:59');    
        }
        
        if(isset($request->maid)){
            $checkSuites->where('maid','like',"%$request->maid%" );    
        }
                      

        $checkSuites= $checkSuites->get();
        return view('event/check_suites/list')->with(['data' => $checkSuites,"filter"=>$filter]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('event/check_suites/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        //salva check suite
        $check_suite = new CheckSuite();
        $check_suite->date = $request->date;
        $check_suite->local_id = $request->local_id;
        $check_suite->user_id = $request->user_id;
        $check_suite->status = $request->status;
        $check_suite->maid = $request->maid;
        $check_suite->obs = $request->obs;
        $check_suite->save();

        //salva items check suite
      
        foreach ( $request->valuation as $key => $value){
            $checkSuiteItems =  new CheckSuiteItem();
            $checkSuiteItems->check_suite_id = $check_suite->id;
            $checkSuiteItems->occurrences_id = $request->occurrences_id[$key];
            $checkSuiteItems->item = $key ;
            $checkSuiteItems->valuation = $value ;
            $checkSuiteItems->register = $request->register[$key] ;
            $checkSuiteItems->save();
        }

        DB::commit();
        return $check_suite;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CheckSuite $checkSuite)
    {
        return view('event/check_suites/view',compact('checkSuite'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(CheckSuite $checkSuite)
    {   
        
        return view('event/check_suites/edit',compact('checkSuite'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CheckSuite $check_suite)
    {
        DB::beginTransaction();

        $check_suite->date = $request->date;
        $check_suite->local_id = $request->local_id;
        $check_suite->user_id = $request->user_id;
        $check_suite->status = $request->status;
        $check_suite->maid = $request->maid;
        $check_suite->obs = $request->obs;
        $check_suite->save();


        //salva items check suite
        CheckSuiteItem::where('check_suite_id',$check_suite->id)->delete();
        foreach ( $request->valuation as $key => $value){
            $checkSuiteItems =  new CheckSuiteItem();
            $checkSuiteItems->check_suite_id = $check_suite->id;
            $checkSuiteItems->occurrences_id = $request->occurrences_id[$key];
            $checkSuiteItems->item = $key ;
            $checkSuiteItems->valuation = $value ;
            $checkSuiteItems->register = $request->register[$key] ;
            $checkSuiteItems->save();
        }

        DB::commit();
        return $check_suite;
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CheckSuite $checkSuite)
    {
        $checkSuite->delete();
        return $checkSuite;   
    }
}
