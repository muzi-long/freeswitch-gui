<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
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
        return $this->hasOne(Merchant::class,'id','merchant_id')->withDefault([
            'company_name' => '-',
        ]);
    }

    /**
     * 部门信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function department()
    {
        return $this->hasOne(Department::class,'id','department_id')->withDefault([
            'name' => '-',
        ]);
    }

    /**
     * 分机信息
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sip()
    {
        return $this->hasOne(Sip::class,'id','sip_id')->withDefault([
            'username' => '-',
        ]);
    }

}
