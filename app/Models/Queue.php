<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Queue extends Model
{
    protected $table = 'queue';
    protected $guarded = ['id'];
    protected $appends = ['strategy_name'];

    public function sips()
    {
        return $this->belongsToMany(Sip::class,'queue_sip');
    }

    public function getStrategyNameAttribute()
    {
        return $this->attributes['strategy_name'] = Arr::get(config('freeswitch.strategy'),$this->strategy);
    }

}
