<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApartmentInspection extends Model
{
    use SoftDeletes;
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
