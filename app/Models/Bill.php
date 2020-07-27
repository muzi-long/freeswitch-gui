<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{

    protected $table = 'bill';
    protected $fillable = [
        'merchant_id',
        'merchant_name',
        'type',
        'money',
        'remark',
        'admin_id',
        'admin_name',
        'total',
    ];

    protected $appends = [
        'money_format',
        'total_format',
    ];

    public function getMoneyFormatAttribute()
    {
        return $this->attributes['money_format'] = round($this->money/100,2);
    }

    public function getTotalFormatAttribute()
    {
        return $this->attributes['total_format'] = round($this->total/100,2);
    }

}
