<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectRemark extends Model
{
    protected $table = 'project_remark';

    public function merchant()
    {
        return $this->hasOne('App\Models\Merchant','id','merchant_id')->withDefault();
    }
}
