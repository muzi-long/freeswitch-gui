<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Node extends Model
{
    use SoftDeletes;
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

    /**
     * 节点所有的项目
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany(Project::class,'node_id','id');
    }

}
