<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $table = 'agent';
    protected $fillable = [
        'display_name',
        'name',
        'originate_type',
        'originate_number',
        'status',
        'max_no_answer',
        'wrap_up_time',
        'reject_delay_time',
        'busy_delay_time',
        'no_answer_delay_time',
    ];

    protected $appends = ['originate_type_name','status_name'];

    public function getOriginateTypeNameAttribute()
    {
        return $this->attributes['originate_type_name'] = array_get([
            'user'      => '分机',
            'group'     => '分机组',
            'gateway'   => '网关',
        ],$this->originate_type);
    }

    public function getStatusNameAttribute()
    {
        return $this->attributes['status_name'] = array_get(config('freeswitch.agent_status'),$this->status);
    }


}
