<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditReportItem1 extends Model
{
  protected $fillable = [
    'audit_report_id',
    'reserve',
    'name',
    'pax',
  ];
}
