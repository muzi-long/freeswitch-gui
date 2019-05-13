<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cdr extends Model
{
    protected $table = 'cdr_a_leg';

    public function bleg()
    {
        return $this->hasOne('App\Models\Bleg','aleg_uuid','aleg_uuid');
    }

    public function getBillsecAttribute($value)
    {
        if (!empty($this->bleg_uuid)){
            $value = $this->bleg->billsec;
        }
        return $value;
    }
    
}
