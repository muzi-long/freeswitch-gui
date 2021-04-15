<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectNode extends Model
{
    protected $table = 'project_node';

    public function oldNode()
    {
        return $this->hasOne('App\Models\Node','id','old')->withDefault(['name'=>'-']);
    }

    public function newNode()
    {
        return $this->hasOne('App\Models\Node','id','new')->withDefault(['name'=>'-']);
    }

    public function merchant()
    {
        return $this->hasOne('App\Models\Merchant','id','merchant_id')->withDefault();
    }

}
