<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Game extends Model
{
    protected $fillable = array('player1', 'player2', 'score1', 'score2');

    public function player1()
    {
        return $this->belongsTo('App\Player', 'player1');
    }
    public function player2()
    {
        return $this->belongsTo('App\Player', 'player2');
    }
}
