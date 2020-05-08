<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRemark extends Model
{
    protected $table = 'project_remark';

    public function user()
    {
        return $this->hasOne('App\Models\User','id','user_id')->withDefault(['nickname'=>'-']);
    }
}
