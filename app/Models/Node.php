<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Node extends Model
{
    protected $table = 'node';
    protected $fillable = [
        'name',
        'sort',
        'type',
    ];
    protected $appends = ['type_name'];

    public function getTypeNameAttribute()
    {
        return $this->attributes['type_name'] = Arr::get([1=>'前台',2=>'后台'],$this->type,'-');
    }

    /**
     * 节点所有的项目
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany('App\Models\Project','node_id','id');
    }
}
