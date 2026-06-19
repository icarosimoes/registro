<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditReportItem3 extends Model
{
  protected $fillable = [
    'audit_report_id',
    'reserve',
    'name',
    'pax',
  ];
}
