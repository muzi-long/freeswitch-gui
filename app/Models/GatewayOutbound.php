<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatewayOutbound extends Model
{
    protected $table = 'gateway_outbound';
    protected $fillable = [
        'gateway_id',
        'status',
        'number',
    ];

    /**
     * 所属网关
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gateway()
    {
        return $this->hasOne('App\Models\Gateway','id','gateway_id')->withDefault([]);
    }

}
