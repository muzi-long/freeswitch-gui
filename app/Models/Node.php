<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Node extends Model
{
    protected $table = 'node';
    protected $guarded = ['id'];
    protected $appends = ['type_name'];

    public function getTypeNameAttribute()
    {
        return $this->attributes['type_name'] = Arr::get(config('freeswitch.node_type'),$this->type,'-');
    }


    public function customer()
    {
        return $this->hasMany(Customer::class,'node_id','id');
    }

}
