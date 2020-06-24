<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    protected $table = 'condition';
    protected $fillable = ['display_name','field','expression','break','extension_id','sort'];
    protected $with = 'actions';

    public function actions()
    {
        return $this->hasMany('App\Models\Action','condition_id','id')->orderBy('sort')->orderBy('id');
    }

}
