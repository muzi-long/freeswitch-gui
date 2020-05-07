<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;
    protected $table = 'project';
    protected $fillable = [
        'company_name',
        'name',
        'phone',
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
        return $this->belongsToMany('App\Models\ProjectDesign','project_design_value','project_id','project_design_id')
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
        return $this->hasOne('App\Models\Node','id','node_id')->withDefault(['name'=>'-']);
    }

    /**
     * 跟进人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function followUser()
    {
        return $this->hasOne('App\Models\User','id','follow_user_id')->withDefault(['nickname'=>'-']);
    }

}
