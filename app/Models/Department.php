<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';
    protected $guarded = ['id'];

    //子部门
    public function child()
    {
        return $this->hasMany('App\Models\Department','parent_id','id');
    }

    //所有子递归
    public function childs()
    {
        return $this->child()->with('childs');
    }

}
