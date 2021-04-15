<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Freeswitch extends Model
{
    use SoftDeletes;
    protected $table = 'freeswitch';
    protected $fillable = [
        'name',
        'external_ip',
        'internal_ip',
        'esl_port',
        'esl_password',
        'internal_sip_port',
        'swoole_http_port',
        'fs_install_path',
        'fs_record_path',
    ];
}
