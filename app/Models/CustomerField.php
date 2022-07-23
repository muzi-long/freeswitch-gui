<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CustomerField extends Model
{
    protected $table = 'customer_field';
    protected $guarded = ['id'];

    protected $appends = ['field_type_name'];

    public function getFieldTypeNameAttribute($value)
    {
        return Arr::get(config('freeswitch.field_type'),$this->field_type,'-');
    }

}
