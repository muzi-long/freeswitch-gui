<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected $table = "permission";
    protected $guarded = ["id"];

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
