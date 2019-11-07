<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Digits extends Model
{
    protected $table = 'digits';
    protected $fillable = ['ivr_id','action','digits','param'];
    protected $appends = ['action_name','ivr_name'];


    public function getActionNameAttribute()
    {
        return $this->attributes['action_name'] = Arr::get(config('freeswitch.ivr_action'),$this->action);
    }

    public function getIvrNameAttribute()
    {
        return $this->attributes['ivr_name'] = $this->ivr->display_name."（".$this->ivr->name.")";
    }

    public function ivr()
    {
        return $this->belongsTo('App\Models\Ivr');
    }

}
