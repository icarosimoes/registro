<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Occurrence extends Model
{
    public function type_occurrences(){
        return $this->belongsTo('App\Models\TypeOccurrence');
    }
    public function users(){
        return $this->belongsTo('App\Models\User');
    }

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by');
    }

    protected static function booted()
    {

        static::creating(function ($occurrence) {
            $occurrence->created_by = Auth::id();
        });

        // static::updating(function ($occurrence) {
        //     $occurrence->updated_by = Auth::id();
        // });

        // static::deleting(function ($occurrence) {
        //     $occurrence->deleted_by = Auth::id();
        //     $occurrence->save();
        // });
    }


}
