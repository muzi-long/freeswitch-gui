<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'group';
    protected $fillable = ['name','display_name'];

    public function sips()
    {
        return $this->belongsToMany('App\Models\Sip','group_sip','group_id','sip_id');
    }

}
