<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffLoginLog extends Model
{
    protected $table = 'staff_login_log';
    protected $fillable = [
        'merchant_id',
        'merchant_company_name',
        'department_id',
        'department_name',
        'staff_id',
        'staff_nickname',
        'staff_username',
        'time',
        'ip',
    ];

}
