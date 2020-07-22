<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cdr extends Model
{

    protected $table = 'cdr';
    protected $fillable = [
        'uuid',
        'aleg_uuid',
        'bleg_uuid',
        'merchant_id',
        'department_id',
        'staff_id',
        'sip_id',
        'merchant_name',
        'department_name',
        'staff_name',
        'caller',
        'callee',
        'call_time',
        'answer_time',
        'end_time',
        'billsec',
        'record_file',
        'user_data',
        'callback_url',
    ];

}
