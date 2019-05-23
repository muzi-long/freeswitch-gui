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
        'moh-sound',
        'record-template',
        'time-base-score',
        'max-wait-time',
        'max-wait-time-with-no-agent',
        'max-wait-time-with-no-agent-time-reached',
    ];
    protected $appends = ['strategy_name'];

    public function agents()
    {
        return $this->belongsToMany('App\Models\Agent','tiers','queue','agent','name','name');
    }

    public function getStrategyNameAttribute()
    {
        return $this->attributes['strategy_name'] = array_get(config('freeswitch.strategy'),$this->strategy);
    }
    
}
