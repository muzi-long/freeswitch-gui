<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{

    protected $table = 'project';

    protected $fillable = [
        'merchant_id',
        'department_id',
        'company_name',
        'contact_name',
        'contact_phone',
        'node_id',
        'follow_at',
        'follow_user_id',
        'next_follow_at',
        'created_user_id',
        'updated_user_id',
        'deleted_user_id',
        'owner_user_id',
    ];

    /**
     * 表单字段
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function designs()
    {
        return $this->belongsToMany(ProjectDesign::class,'project_design_value','project_id','project_design_id')
            ->where('visiable',1)
            ->orderBy('sort','asc')
            ->withPivot(['id','data']);
    }

    /**
     * 当前节点
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function node()
    {
        return $this->hasOne(Node::class,'id','node_id')->withDefault(['name'=>'-']);
    }

    /**
     * 跟进人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function followUser()
    {
        return $this->hasOne(Staff::class,'id','follow_user_id')->withDefault(['nickname'=>'-']);
    }

}
