<?php

namespace App\Http\Controllers\Event\InspectionSuites;

use App\Exports\InspectionSuiteExcelExport;
use App\Http\Controllers\Controller;
use App\Local;
use App\Models\InspectionSuite;
use App\Models\InspectionSuiteItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class InspectionSuiteController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    //dd('asd');
    $this->authorize('index', InspectionSuite::class);
    $filter = $request->all();

    $checkSuites = InspectionSuite::orderBy('id', 'DESC');

    if (isset($request->local)) {
      $checkSuites->where('local_id', $request->local);
      $filter['local'] = Local::find($request->local);
    }

    if (isset($request->user)) {
      $checkSuites->where('user_id', $request->user);
      $filter['user'] = User::find($request->user);
    }

    if (isset($request->date_start) && $request->date_start != null) {
      $checkSuites->where('date', '>=', $request->date_start);
    }

    if (isset($request->date_end) && $request->date_end != null) {
      $checkSuites->where('date', '<=', $request->date_end . ' 23:59:59');
    }

    if (isset($request->maid)) {
      $checkSuites->where('maid', 'like', "%$request->maid%");
    }

    $checkSuites = $checkSuites->get();
    session()->put('check_suites', $checkSuites);
    return view('event/inspection_suites/list')->with(['data' => $checkSuites, "filter" => $filter]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $this->authorize('store', InspectionSuite::class);
    return view('event/inspection_suites/create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->authorize('store', InspectionSuite::class);
    DB::beginTransaction();

    //salva check suite
    $inspection_suite = new InspectionSuite();
    $inspection_suite->date = $request->date;
    $inspection_suite->local_id = $request->local_id;
    $inspection_suite->user_id = $request->user_id;
    $inspection_suite->status = $request->status;
    $inspection_suite->maid = $request->maid;
    $inspection_suite->obs = $request->obs;
    $inspection_suite->save();

    //salva items inspection suite

    foreach ($request->valuation as $key => $value) {
      $inspectionSuiteItems =  new InspectionSuiteItem();
      $inspectionSuiteItems->inspection_suite_id = $inspection_suite->id;
      $inspectionSuiteItems->occurrences_id = $request->occurrences_id[$key];
      $inspectionSuiteItems->item = $key;
      $inspectionSuiteItems->valuation = $value;
      $inspectionSuiteItems->register = $request->register[$key];
      $inspectionSuiteItems->save();
    }

    DB::commit();
    return $inspection_suite;
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(InspectionSuite $inspectionSuite)
  {
    $this->authorize('show', InspectionSuite::class);
    return view('event/inspection_suites/view', compact('inspectionSuite'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(InspectionSuite $inspectionSuite)
  {
    $this->authorize('show', InspectionSuite::class);
    return view('event/inspection_suites/edit', compact('inspectionSuite'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, InspectionSuite $inspection_suite)
  {
    $this->authorize('update', InspectionSuite::class);
    DB::beginTransaction();

    $inspection_suite->date = $request->date;
    $inspection_suite->local_id = $request->local_id;
    $inspection_suite->user_id = $request->user_id;
    $inspection_suite->status = $request->status;
    $inspection_suite->maid = $request->maid;
    $inspection_suite->obs = $request->obs;
    $inspection_suite->save();


    //salva items inspection suite
    InspectionSuiteItem::where('inspection_suite_id', $inspection_suite->id)->delete();
    foreach ($request->valuation as $key => $value) {
      $inspectionSuiteItems =  new inspectionSuiteItem();
      $inspectionSuiteItems->inspection_suite_id = $inspection_suite->id;
      $inspectionSuiteItems->occurrences_id = $request->occurrences_id[$key];
      $inspectionSuiteItems->item = $key;
      $inspectionSuiteItems->valuation = $value;
      $inspectionSuiteItems->register = $request->register[$key];
      $inspectionSuiteItems->save();
    }

    DB::commit();
    return $inspection_suite;
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(InspectionSuite $inspectionSuite)
  {
    $this->authorize('delete', InspectionSuite::class);
    $inspectionSuite->delete();
    return $inspectionSuite;
  }


  /**
   * export excel
   */
  public function exportExcel()
  {
    $inspection_suite = session()->get('check_suites');
    $name = request()->description;
    return Excel::download(new InspectionSuiteExcelExport($inspection_suite, $name), 'relatorio.xlsx');
  }
}
