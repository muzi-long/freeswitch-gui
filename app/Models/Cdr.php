<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cdr extends Model
{
    protected $table = 'cdr_a_leg';
    protected $guarded = ['id'];

    public function bleg()
    {
        return $this->hasOne('App\Models\Bleg','bleg_uuid','bleg_uuid')->withDefault([]);
    }



}
