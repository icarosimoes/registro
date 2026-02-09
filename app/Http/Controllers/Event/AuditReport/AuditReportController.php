<?php

namespace App\Http\Controllers\Event\AuditReport;

use App\AuditReport;
use App\AuditReportItem1;
use App\AuditReportItem2;
use App\AuditReportItem3;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditReportController extends Controller
{


  public function index()
  {
    $auditReports = AuditReport::orderBy('id', 'desc')->paginate(25);
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

    //salva os items dos 2 lista
    $dataTable2 = json_decode($request->dataTable2, true);

    $dataTableIds = collect($dataTable2)->pluck('id');

    AuditReportItem2::where('audit_report_id', $auditReport->id)
      ->whereNotIn('id', $dataTableIds)
      ->delete();

    foreach ($dataTable2 as $item) {
      AuditReportItem2::updateOrCreate(
        ['id' => @$item['id']],
        [
          'audit_report_id' => $auditReport->id,
          'name' => $item['name'],
          'pax' => $item['pax'],
        ]
      );
    }

    //salva os items dos 3 lista
    $dataTable3 = json_decode($request->dataTable3, true);

    $dataTableIds = collect($dataTable3)->pluck('id');

    AuditReportItem3::where('audit_report_id', $auditReport->id)
      ->whereNotIn('id', $dataTableIds)
      ->delete();

    foreach ($dataTable3 as $item) {
      AuditReportItem3::updateOrCreate(
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
    $auditReport = AuditReport::find($id);
    return view('event.audit_report.edit', compact('auditReport'));
  }

  public function update(AuditReport $auditReport, Request $request)
  {
    DB::beginTransaction();
    $request->merge(['user_id' => auth()->id()]);
    $auditReport->update($request->all());

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

    //salva os items dos 2 lista
    $dataTable2 = json_decode($request->dataTable2, true);

    $dataTableIds = collect($dataTable2)->pluck('id');

    AuditReportItem2::where('audit_report_id', $auditReport->id)
      ->whereNotIn('id', $dataTableIds)
      ->delete();

    foreach ($dataTable2 as $item) {
      AuditReportItem2::updateOrCreate(
        ['id' => @$item['id']],
        [
          'audit_report_id' => $auditReport->id,
          'name' => $item['name'],
          'pax' => $item['pax'],
        ]
      );
    }

    //salva os items dos 3 lista
    $dataTable3 = json_decode($request->dataTable3, true);

    $dataTableIds = collect($dataTable3)->pluck('id');

    AuditReportItem3::where('audit_report_id', $auditReport->id)
      ->whereNotIn('id', $dataTableIds)
      ->delete();

    foreach ($dataTable3 as $item) {
      AuditReportItem3::updateOrCreate(
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

  public function destroy($id)
  {


    $auditReport = AuditReport::find($id);
    $auditReport->audit_report_item_1s()->delete();
    $auditReport->audit_report_item_2s()->delete();
    $auditReport->audit_report_item_3s()->delete();
    $auditReport->delete();
    return response('success');
  }
}
