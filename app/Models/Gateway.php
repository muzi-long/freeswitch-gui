<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $table = 'gateway';
    protected $fillable = [
        'name',
        'realm',
        'username',
        'password',
        'prefix',
        'outbound_caller_id',
        'type',
        'freeswitch_id',
        'merchant_id',
    ];

    /**
     * 所属网关
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function freeswitch()
    {
        return $this->hasOne(Freeswitch::class,'id','freeswitch_id')->withDefault([
            'name' => '-',
        ]);
    }

    /**
     * 所属商户
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function merchant()
    {
        return $this->hasOne(Merchant::class,'id','merchant_id')->withDefault([
            'company_name' => '-',
        ]);
    }

}
