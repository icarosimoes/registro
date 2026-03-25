<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApartmentInspectionItems_v2 extends Model
{
    

    protected $fillable = [
        'apartment_inspection_id',
        'group',
        'service',
        'item_verification',
        'appreciation',
        'approved',
        'occurrence_id'
    ];
}
