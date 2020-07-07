<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';
    protected $fillable = [
        'merchant_id',
        'parent_id',
        'parent_id',
        'name',
        'sort',
    ];
}
