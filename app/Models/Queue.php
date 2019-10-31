<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $table = 'queue';
    protected $fillable = [
        'display_name',
        'name',
        'strategy',
        'max_wait_time',
    ];
    protected $appends = ['strategy_name'];


    public function agents()
    {
        return $this->belongsToMany('App\Models\Agent','queue_agent');
    }

    public function getStrategyNameAttribute()
    {
        return $this->attributes['strategy_name'] = array_get(config('freeswitch.strategy'),$this->strategy);
    }


}
