<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;
    protected $table = 'staff';
    protected $fillable = [
        'merchant_id',
        'is_merchant',
        'username',
        'password',
        'nickname',
        'last_login_at',
        'last_login_ip',
        'department_id',
        'sip_id',
    ];

    /**
     * 商户信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function merchant()
    {
        return $this->hasOne(Merchant::class,'id','merchant_id');
    }
}
