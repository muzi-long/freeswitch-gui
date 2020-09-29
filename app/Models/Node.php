<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class Node extends Model
{
    use SoftDeletes;
    protected $table = 'node';

    protected $fillable = [
        'merchant_id',
        'name',
        'sort',
        'type',
        'created_staff_id',
    ];

    protected $appends = ['type_name'];

    public function getTypeNameAttribute()
    {
        return $this->attributes['type_name'] = Arr::get(config('freeswitch.node_type'),$this->type,'-');
    }

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
