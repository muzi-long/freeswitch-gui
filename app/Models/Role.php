<?php
namespace App\Models;

class Role extends \Spatie\Permission\Models\Role
{

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
