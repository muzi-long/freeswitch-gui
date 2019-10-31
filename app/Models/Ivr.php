<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ivr extends Model
{
    protected $table = 'ivr';
    protected $fillable = [
        'display_name',
        'name',
        'greet_long',
        'greet_short',
        'invalid_sound',
        'exit_sound',
        'confirm_macro',
        'confirm_key',
        'tts_engine',
        'tts_voice',
        'confirm_attempts',
        'timeout',
        'inter_digit_timeout',
        'max_failures',
        'max_timeouts',
        'digit_len',
    ];

    public function digits()
    {
        return $this->hasMany('App\Models\Digits');
    }

}
