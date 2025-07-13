<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApartmentInspection extends Model
{
    protected $fillable = [
        'owner',
        'unit',
        'inspected_by',
        'inspection_date',
        'observation',
        'approved',
    ];


    function apartment_inspection_items(){
        return $this->hasMany(ApartmentInspectionItem::class);
    }

}
