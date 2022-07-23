<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $table = 'task_call';
    protected $guarded = ['id'];

    public function sip()
    {
        return $this->hasOne('App\Models\Sip','id','sip_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User','id','user_id');
    }

}
