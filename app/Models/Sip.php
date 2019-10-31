<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sip extends Model
{
    protected $table = 'sip';
    protected $fillable = [
        'username',
        'password',
        'effective_caller_id_name',
        'effective_caller_id_number',
        'outbound_caller_id_name',
        'outbound_caller_id_number',
        'merchant_id',
        'gateway_id',
        'expense_id',
    ];


}
