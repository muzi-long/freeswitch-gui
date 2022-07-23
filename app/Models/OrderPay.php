<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class OrderPay extends Model
{
    protected $table = 'order_pay';
    protected $guarded = ['id'];

    protected $appends = ['pay_type_name','status_name'];

    public function getPayTypeNameAttribute()
    {
        return $this->attributes['pay_type_name'] = Arr::get(config('freeswitch.pay_type'),$this->pay_type,'-');
    }

    public function getStatusNameAttribute()
    {
        return $this->attributes['status_name'] = Arr::get([0=>'待审核',1=>'审核通过',2=>'审核不通过'],$this->status,'-');
    }

    public function order()
    {
        return $this->hasOne(Order::class,'id','order_id');
    }

}
