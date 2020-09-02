<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFollow extends Model
{
    protected $table = 'project_follow';
    protected $fillable = [
        'project_id',
        'old_node_id',
        'old_node_name',
        'new_node_id',
        'new_node_name',
        'content',
        'next_follow_at',
        'staff_id',
    ];

    public function staff()
    {
        return $this->hasOne(Staff::class,'id','staff_id')->withDefault(['nickname' => '-']);
    }

}
