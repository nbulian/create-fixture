<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'team';

    public function image()
    {
        return $this->hasMany('App\Image', 'id_team');
    }
}
