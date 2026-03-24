<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApartmentInspectionsV2 extends Model
{
  
    protected $fillable = [
        'owner',
        'unit',
        'inspected_by',
        'inspection_date',
        'type_unit',
        'observation',
        'approved'
    ];  

}
