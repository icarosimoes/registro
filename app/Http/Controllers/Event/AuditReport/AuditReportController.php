<?php

namespace App\Http\Controllers\Event\AuditReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditReportController extends Controller
{
    public function index()
    {
        
        return view('event.audit_report.list');
    }

    public function show($id)
    {
        return view('event.audit_report.show', ['id' => $id]);
    }

    public function create()
    {
        return view('event.audit_report.create');
    }

    public function store()
    {
        return view('event.audit_report.create');
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
