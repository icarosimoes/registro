<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditReport extends Model
{
  protected $fillable = [
    'date',
    'occupation',
    'average_daily',
    'guests',
    'uh',
    'maintenance_apartment',
    'cleaning',
    'walk_in',
    'obs',
    'AB',
    'reception',
    'reservations',
    'governance',
    'housekeeping',
    'maintenance',
    'ti',
    'security',
    'user_id'
  ];
  public function user()
  {
    return $this->belongsTo(User::class);
  }
  public function audit_report_item_1s()
  {
    return $this->hasMany(AuditReportItem1::class);
  }
  public function audit_report_item_2s()
  {
    return $this->hasMany(AuditReportItem2::class);
  }
  public function audit_report_item_3s()
  {
    return $this->hasMany(AuditReportItem3::class);
  }
  
}
