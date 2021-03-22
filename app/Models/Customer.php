<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';
    protected $guarded = ['id'];

    public function fields()
    {
        return $this->belongsToMany('App\Models\CustomerField','customer_field_value','customer_id','customer_field_id')
            ->where('visiable',1)
            ->orderBy('sort','asc')
            ->withPivot(['id','data']);
    }

}
