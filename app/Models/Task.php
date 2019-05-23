<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'task';
    protected $fillable = [
        'name',
        'datetime_start',
        'datetime_end',
        'gateway_id',
        'queue_id',
        'max_channel',
        'status',
    ];
    protected $appends = ['gateway_name','queue_name'];

    public function gateway()
    {
        return $this->hasOne('App\Models\Gateway','id','gateway_id');
    }

    public function queue()
    {
        return $this->hasOne('App\Models\Queue','id','queue_id');
    }

    public function getGatewayNameAttribute()
    {
        return $this->attributes['gateway_name'] = $this->gateway->name;
    }

    public function getQueueNameAttribute()
    {
        return $this->attributes['queue_name'] = $this->queue->display_name;
    }

    //总呼叫数
    public function calls()
    {
        return $this->hasMany('App\Models\Call','task_id','id');
    }

    //已呼叫数 status !=1
    public function hasCalls()
    {
        return $this->hasMany('App\Models\Call','task_id','id')->where('status','!=',1);
    }

    //漏接数 status=3
    public function missCalls()
    {
        return $this->hasMany('App\Models\Call','task_id','id')->where('status',3);
    }

    //呼叫成功数 status=4
    public function successCalls()
    {
        return $this->hasMany('App\Models\Call','task_id','id')->where('status',4);
    }

    //呼叫失败数 status=5
    public function failCalls()
    {
        return $this->hasMany('App\Models\Call','task_id','id')->where('status',5);
    }

}
