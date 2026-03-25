<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApartmentInspectionsV2 extends Model
{
    protected $table = 'apartment_inspections_v2s';
    protected $fillable = [
        'owner',
        'unit',
        'inspected_by',
        'inspection_date',
        'type_unit',
        'observation',
        'approved'
    ];
    
    public function apartmentInspectionItems()
    {
        return $this->hasMany(ApartmentInspectionItems_v2::class, 'apartment_inspection_id', 'id');
    }

}
