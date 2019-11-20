<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Extension extends Model
{
    protected $table = 'extension';
    protected $fillable = ['display_name','name','sort','continue','context'];
    protected $appends = ['context_name'];

    public function getContextNameAttribute()
    {
        return Arr::get(['default'=>'呼出','public'=>'呼入'],$this->context);
    }

    public function conditions()
    {
        return $this->hasMany('App\Models\Condition','extension_id','id')->orderBy('sort')->orderBy('id');
    }
}
