<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'task';
    protected $guarded = ['id'];

    protected $appends = ['gateway_name','queue_name','date','time'];

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
        return $this->attributes['queue_name'] = $this->queue->name;
    }

    public function getDateAttribute()
    {
        return $this->attributes['date'] = $this->date_start . ' / '. $this->date_end;
    }

    public function getTimeAttribute()
    {
        return $this->attributes['time'] = $this->time_start . ' - '. $this->time_end;
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

    //呼叫失败数 status=2
    public function failCalls()
    {
        return $this->hasMany('App\Models\Call','task_id','id')->where('status',2);
    }

}
