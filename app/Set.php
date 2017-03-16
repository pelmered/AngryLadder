<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Set extends Model
{
    protected $fillable = array('score1', 'score2');

    public function game()
    {
        return $this->belongsTo('App\Match');
    }
}
