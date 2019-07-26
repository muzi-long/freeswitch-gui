<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'bill';
    protected $fillable = ['merchant_id','type','money','remark','created_user_id'];
    protected $appends = ['created_user_name'];

    public function getCreatedUserNameAttribute()
    {
        return $this->attributes['created_user_name'] = $this->user->name??'系统操作';
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_user_id', 'id')->withDefault();
    }

    public function merchant()
    {
        return $this->belongsTo('App\Models\Merchant', 'merchant_id', 'id');
    }

}
