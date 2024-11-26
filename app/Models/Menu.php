<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';
    protected $guarded = ['id'];
    //子菜单
    public function child()
    {
        return $this->hasMany('App\Models\Menu','parent_id','id');
    }

    //所有菜单递归
    public function childs()
    {
        return $this->child()->with('childs');
    }
}
