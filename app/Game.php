<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

use App\AngryLadder\Elo;

class Game extends Model
{
    protected $fillable = array('winner');

    public function players()
    {
        return $this->belongsToMany('App\Player');
    }

    public function sets()
    {
        return $this->hasMany('App\Set');
    }

    public static function createNewGame($players, $scoreData )
    {
        $winner = 0;
        $p1 = 0;
        $p2 = 0;

        $sets = [];

        foreach( $scoreData AS $score )
        {
            $set = [
                'score1' => $score['set'][0],
                'score2' => $score['set'][1]
            ];

            if( $set['score1'] > $set['score2'] )
            {
                $p1++;
            }
            else
            {
                $p2++;
            }

            $sets[] = $set;

            if( $p1 >= 2 )
            {
                $winner = 1;
                break;
            }
            if( $p2 >= 2 )
            {
                $winner = 2;
                break;
            }
        }

        if( $winner < 0 )
        {
            return ['error' => 'No winner in game. Plz play moar!' ];
        }

        if( isset($players['error']) )
        {
            return ['error' => 'Error: Player could not be found: ' . print_r($players['error'], true) ];
        }

        $p = 0;

        foreach($players AS $player)
        {

            if( is_array($player) )
            {
                $playerObj = $players[$p];
                foreach($player AS $key => $value)
                {
                    $playerObj->$key = $value;
                }
                $playerObj->save();
                $players[$p++] = $playerObj;
            }
        }

        $game = Game::create([
            'winner'    => $winner
        ]);

        // Add sets with relation to game
        foreach($players AS $player)
        {
            $game->players()->attach( $player->id );
        }


        // Add sets with relation to game
        foreach($sets AS $set)
        {
            $game->sets()->create( $set );
        }

        //$data = $game->toArray();

        $elo = new Elo( );
        $new_rankings = $elo->calculateGame( $game );

        $players[0]->adjustRating ( $new_rankings['player1'] );
        $players[1]->adjustRating ( $new_rankings['player2'] );

        $game->rating_adjustment_player1 = $new_rankings['player1'];
        $game->rating_adjustment_player2 = $new_rankings['player2'];

        $game->save();

        return $game;
    }

    public static function create(array $attributes = [])
    {
        return parent::create($attributes);
    }
}
