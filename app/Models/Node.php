<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    protected $table = 'node';
    protected $fillable = ['name','sort','merchant_id'];

    /**
     * 节点所有的项目
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany('App\Models\Project','node_id','id');
    }

}
