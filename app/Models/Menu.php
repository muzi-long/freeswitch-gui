<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $fillable = [
        'name',
        'parent_id',
        'route',
        'url',
        'icon',
        'sort',
        'type',
        'permission_id',
        'guard',
    ];

    //子权限
    public function child()
    {
        return $this->hasMany('App\Models\Menu','parent_id','id');
    }

    //所有子权限递归
    public function childs()
    {
        return $this->child()->with('childs');
    }

}
