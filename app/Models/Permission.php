<?php
namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;

class Permission extends \Spatie\Permission\Models\Permission
{

    protected $appends = ['type_name','visiable_name'];

    public function getTypeNameAttribute()
    {
        return $this->attributes['type_name'] = Arr::get([1=>'按钮',2=>'菜单'],$this->type);
    }
    public function getVisiableNameAttribute()
    {
        return $this->attributes['visiable_name'] = Arr::get([1=>'显示',2=>'隐藏'],$this->visiable);
    }

    //子权限
    public function childs()
    {
        return $this->hasMany('App\Models\Permission','parent_id','id');
    }

    //所有子权限递归
    public function allChilds()
    {
        return $this->childs()->with('allChilds');
    }

}