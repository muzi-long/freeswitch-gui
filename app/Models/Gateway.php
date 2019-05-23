<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $table = 'gateway';
    protected $fillable = ['name','realm','username','password','prefix','outbound_caller_id'];

}
