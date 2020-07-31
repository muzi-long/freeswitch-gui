<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    protected $table = 'node';

    protected $fillable = [
        'merchant_id',
        'name',
        'sort',
        'created_staff_id',
    ];

    /**
     * 所属商户
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function merchant()
    {
        return $this->hasOne(Merchant::class,'id','merchant_id')->withDefault([
            'company_name' => '-'
        ]);
    }

}
