<?php

namespace App\Models;

use App\Local;
use App\Sector;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Occurrence extends Model
{
    public function type_occurrences()
    {
        return $this->belongsTo('App\Models\TypeOccurrence');
    }
    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    public function local()
    {
        return $this->belongsTo(Local::class, 'local_id');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public function scopeSelectSearch($query, $request)
    {
        preg_match('/\d+/', $request->term, $match3);

        if (!empty($match3)) {
            $code = $match3[0];
            return $query->where('id', 'like', "%$code%");
        } else {
            if ($request->term != null) {
                return $query->where('title', 'like', "%$request->term%");
            }
        }
    }

    protected static function booted()
    {

        static::creating(function ($occurrence) {
            $occurrence->created_by = Auth::id();
        });

        static::updating(function ($occurrence) {
            
            $occurrence->updated_by = Auth::id();

            if (!empty(request()->participants)) {
                $participants = explode(",", request()->participants);
                
                foreach ($participants  as  $participant) {

                    $notification = new Notification();
                    $notification->occurrence_id = $occurrence->id;
                    $notification->checked = 'not';
                    $notification->user_id = $participant;
                    $notification->save();
                }
            }
        });
    }
}
