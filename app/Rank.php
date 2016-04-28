<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

use App\AngryLadder\Elo;

class Rank extends Model
{
    protected $fillable = array('weekly', 'allTime');

    public function __construct()
    {
        //$this->rank = $rank;

        parent::__construct();
    }

    public function players()
    {
        return $this->belongsTo('App\Player');
    }
}
