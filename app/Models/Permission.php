<?php
namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{

    //子权限
    public function child()
    {
        return $this->hasMany('App\Models\Permission','parent_id','id');
    }

    //所有子权限递归
    public function childs()
    {
        return $this->child()->with('childs');
    }

}