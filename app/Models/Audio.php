<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    protected $table = 'audio';
    protected $fillable = [
        'url',
        'text',
        'auf',
        'aue',
        'voice_name',
        'speed',
        'volume',
        'pitch',
        'engine_type',
    ];
}
