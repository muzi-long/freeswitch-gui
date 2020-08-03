<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class ProjectDesign extends Model
{
    use SoftDeletes;
    protected $table = 'project_design';
    protected $fillable = [
        'merchant_id',
        'field_label',
        'field_key',
        'field_type',
        'field_option',
        'field_value',
        'field_tips',
        'sort',
        'visiable',
        'required',
    ];
    protected $appends = ['field_type_name'];

    public function getFieldTypeNameAttribute($value)
    {
        return Arr::get(config('freeswitch.field_type'),$this->field_type,'-');
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

}
