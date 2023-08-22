<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkDiaryActivity extends Model
{
    protected $fillable = [
        'sector',
        'description',
        'register',
        'attachment'
    ];
}
