<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ProjectDesign extends Model
{
    protected $table = 'project_design';
    protected $fillable = [
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

}
