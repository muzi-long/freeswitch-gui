<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $table = 'agents';
    protected $fillable = [
        'name',
        'contact',
        'status',
        'state',
        'max_no_answer',
        'wrap_up_time',
        'reject_delay_time',
        'busy_delay_time',
        'no_answer_delay_time'
    ];
    protected $appends = ['contact_name','status_name','state_name'];

    public function getContactNameAttribute()
    {
        return $this->attributes['contact_name'] = str_after($this->contact,'user/');
    }

    public function getStatusNameAttribute()
    {
        return $this->attributes['status_name'] = array_get(config('freeswitch.agent_status'),$this->status);
    }

    public function getStateNameAttribute()
    {
        return $this->attributes['state_name'] = array_get(config('freeswitch.agent_state'),$this->state);
    }

}
