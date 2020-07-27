<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $table = 'rate';
    protected $fillable = [
        'name',
        'description',
        'cost',
        'time',
    ];
    protected $appends = ['cost_format'];

    public function getCostFormatAttribute()
    {
        return $this->attributes['cost_format'] = round($this->cost/100,2);
    }

}
