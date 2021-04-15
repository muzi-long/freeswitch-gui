<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Action extends Model
{
    protected $table = 'action';
    protected $fillable = ['display_name','application','data','sort','condition_id'];
    protected $appends = ['application_name'];

    public function getApplicationNameAttribute()
    {
        return Arr::get(config('freeswitch.application'),$this->application)."（".$this->application."）";
    }

}
