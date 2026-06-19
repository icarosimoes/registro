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

    public function apartmentInspection()
    {
        return $this->belongsTo(ApartmentInspectionsV2::class, 'apartment_inspection_id', 'id');
    }

    public function atachments()
    {
        return $this->hasMany(ApartamentInspectionItemAttach::class, 'apartment_item_id', 'id');
    }
}


