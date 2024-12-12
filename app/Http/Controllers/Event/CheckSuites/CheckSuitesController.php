<?php

namespace App\Http\Controllers\Event\CheckSuites;

use App\CheckSuite;
use App\CheckSuiteItem;
use App\Exports\CheckSuitesExcelExport;
use App\Http\Controllers\Controller;
use App\Local;
use App\Models\User;
use CreateCheckSuiteItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class CheckSuitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('index',CheckSuite::class);
        $filter = $request->all();

        $checkSuites = CheckSuite::orderBy('id','DESC');

        $local = session()->get('filter_local');
        $user = session()->get('filter_user');
        $date_start = session()->get('filter_date_start');
        $date_end = session()->get('filter_date_end');
        $maid = session()->get('filter_maid');
        
            if(!isset(request()->page)){
                    // salva o filtro na sessao pra manter o filtro durante a paginacao
                    session()->put('filter_local',request()->local);
                    session()->put('filter_user',request()->user);
                    session()->put('filter_date_start',request()->date_start);
                    session()->put('filter_date_end',request()->date_end);
                    session()->put('filter_maid',request()->maid);

                    $local = request()->local;
                    $user = request()->user;
                    $date_start = request()->date_start;
                    $date_end = request()->date_end;
                    $maid = request()->maid;
            }

        if(isset($local)){
            $checkSuites->where('local_id',$local);    
            $filter['local']= Local::find($local);
        }

        if(isset($user)){
            $checkSuites->where('user_id',$user); 
            $filter['user']= User::find($user);   
        }
        
        if(isset($date_start ) && $date_start != null){
            $checkSuites->where('date','>=',$date_start);    
            $filter['date_start']= $date_start; 
            
        }

        if(isset($date_end) && $date_end != null ){
            $checkSuites->where('date','<=',$date_end .' 23:59:59');    
            $filter['date_end']= $date_end; 
        }
        
        if(isset($maid)){
            $checkSuites->where('maid','like',"%$maid%" );    
            $filter['maid']= $maid; 
        }
                
        $checkSuitesExport = $checkSuites->get();
        $checkSuites= $checkSuites->paginate(20);
        session()->put('check_suites',$checkSuitesExport);
        return view('event/check_suites/list')->with(['data' => $checkSuites,"filter"=>$filter]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('store',CheckSuite::class);
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
        $this->authorize('store',CheckSuite::class);
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
        $this->authorize('show',CheckSuite::class);
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
        $this->authorize('show',CheckSuite::class);
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
        $this->authorize('update',CheckSuite::class);
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
        $this->authorize('delete',CheckSuite::class);
        $checkSuite->delete();
        return $checkSuite;   
    }


   /**
   * export excel
   */
  public function exportExcel()
  {
    $check_suites = session()->get('check_suites');
    $name = request()->description;
    return Excel::download(new CheckSuitesExcelExport($check_suites, $name), 'relatorio.xlsx');
  }

  /**
   * export pdf
   */
  public function exportPdf(){

      $check_suites = session()->get('check_suites');
      //dd($check_suites);
      $description = request()->description;  
      $pdf = PDF::loadView('event/check_suites/export_pdf', compact('description','check_suites'))
      ->setPaper('a4');
    
      return $pdf->stream('relatorio.pdf');
  }
}
