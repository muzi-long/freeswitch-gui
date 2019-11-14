<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantInfo extends Model
{
    protected $table = 'merchant_info';
    protected $fillable = [
        'merchant_id',
        'company_name',
        'expires_at',
        'sip_num',
        'member_num',
        'queue_num',
    ];

}
