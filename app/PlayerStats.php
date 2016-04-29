<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

use App\AngryLadder\Elo;

class PlayerStats extends Model
{
    //protected $fillable = array('weekly', 'allTime');

    public function __construct()
    {
        //$this->rank = $rank;

        parent::__construct();
    }

    static public function calculate( $playerId )
    {


        /*
        $games = Game::with('players', 'sets')
            ->orderBy('updated_at', 'desc')
            ->get();
        */

        $stats = [
            'wins'          => 0,
            'loses'         => 0,
            'set_wins'      => 0,
            'set_loses'     => 0
        ];


        $games = Player::find($playerId)->games()->get();


        foreach( $games AS $game )
        {


            $game_array = Game::with('players', 'sets')->find($game->id)->toArray();

            $player_num = ( $game_array['players'][0]['id'] == $playerId ? 1 : 2 );


            if( $game_array['winner'] == $player_num )
            {
                $stats['wins']++;
            }
            else
            {
                $stats['loses']++;
            }

            foreach( $game_array['sets'] AS $set )
            {
                if( $player_num == 1 && $set['score1'] > $set['score2'] )
                {
                    $stats['set_wins']++;
                }
                else
                {
                    $stats['set_loses']++;
                }
            }

        }


        return $stats;
    }

    public function players()
    {
        return $this->belongsTo('App\Player');
    }
}
