<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $table = 'call';

    protected $guarded = ['id'];

    public function agent()
    {
        return $this->hasOne('App\Models\Agent','id','agent_id')->withDefault(['display_name'=>'-']);
    }

}
