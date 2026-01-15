<?php

namespace App\Http\Controllers\Event\AuditReport;

use App\AuditReport;
use App\AuditReportItem1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditReportController extends Controller
{


  public function index()
  {
    $auditReports = AuditReport::paginate(25);
    return view('event.audit_report.list', compact('auditReports'));
  }

  public function show($id)
  {
    return view('event.audit_report.show', ['id' => $id]);
  }

  public function create()
  {
    return view('event.audit_report.create');
  }

  public function store(Request $request)
  {
    DB::beginTransaction();
    $request->merge(['user_id' => auth()->id()]);
    $auditReport =  AuditReport::create($request->all());

    //salva os items dos 1 lista
    $dataTable1 = json_decode($request->dataTable1, true);

    $dataTableIds = collect($dataTable1)->pluck('id');

    AuditReportItem1::where('audit_report_id', $auditReport->id)
      ->whereNotIn('id', $dataTableIds)
      ->delete();

    foreach ($dataTable1 as $item) {
      AuditReportItem1::updateOrCreate(
        ['id' => @$item['id']],
        [
          'audit_report_id' => $auditReport->id,
          'reserve' => $item['reserve'],
          'name' => $item['name'],
          'pax' => $item['pax'],
        ]
      );
    }






    DB::commit();
    return response('success');
  }

  public function edit($id)
  {
    return view('event.audit_report.edit', ['id' => $id]);
  }

  public function update($id)
  {
    return view('event.audit_report.update', ['id' => $id]);
  }

  public function destroy($id)
  {
    return view('event.audit_report.destroy', ['id' => $id]);
  }
}
