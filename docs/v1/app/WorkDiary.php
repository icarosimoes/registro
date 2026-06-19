<?php

namespace App;

use App\Models\WorkDiaryShiftTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
class WorkDiary extends Model
{
    use SoftDeletes;

    public function work_diary_shift_time(){
        return $this->hasMany(WorkDiaryShiftTime::class);
    }

    public function work_diary_frequency_adm(){
        return $this->hasMany(WorkDiaryFrequencyAdm::class);
    }

    public function work_diary_frequency_prod(){
        return $this->hasMany(WorkDiaryFrequencyProd::class);
    }

    public function work_diary_sub(){
        return $this->hasMany(WorkDiarySub::class);
    }
    
    public function work_diary_equipament(){
        return $this->hasMany(WorkDiaryEquipament::class);
    }
    
    public function work_diary_activity(){
        return $this->hasMany(WorkDiaryActivity::class);
    }
    
    public function work_diary_obs(){
        return $this->hasMany(WorkDiaryObs::class);
    }
    
}
