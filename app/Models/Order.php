<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $table = 'order';
    protected $fillable = [
        'company_name',
        'name',
        'phone',
        'project_id',
        'node_id',
        'created_user_id',
        'handle_user_id',
        'handle_time',
        'accept_user_id',
        'accept_time',
        'accept_result',
        'follow_at',
        'follow_user_id',
        'next_follow_at',
        'remark',
    ];

    /**
     * 当前节点
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function node()
    {
        return $this->hasOne('App\Models\Node','id','node_id')->withDefault(['name'=>'-']);
    }

    /**
     * 成单人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function createUser()
    {
        return $this->hasOne('App\Models\User','id','created_user_id')->withDefault(['nickname'=>'-']);
    }

    /**
     * 接单人
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function acceptUser()
    {
        return $this->hasOne('App\Models\User','id','accept_user_id')->withDefault(['nickname'=>'-']);
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
