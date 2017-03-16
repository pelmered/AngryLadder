<?php

namespace App;

//use Illuminate\Contracts\Queue\Queue;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

use App\AngryLadder\Elo;
use pelmered\APIHelper\Traits\APIModel;

use App\Jobs\RefreshPlayerStats;
//use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Queue;

use App\AngryLadder\Glicko2;

//use Pelmered\Glicko2\Glicko2;

//use Pelmered\Glicko2\Glicko2;
//use Zelenin\Glicko2\Match;
//use Pelmered\Glicko2\MatchCollection;
//use Zelenin\Glicko2\Player;

use App\Player;

class Match extends Model
{
    use APIModel;

    protected $fillable = array('winner');

    /*
    public function players()
    {
        return $this->belongsToMany('App\Player');
    }
    */
    public function player1()
    {
        return $this->hasOne('App\Player', 'id', 'player1_id');
        return $this->belongsToMany('App\Player');
    }
    public function player2()
    {
        return $this->hasOne('App\Player', 'id', 'player2_id');
        return $this->belongsToMany('App\Player');
    }
    public function getPlayer1( )
    {
        //dd($this->player1());
        return $this->player1()->first();
    }
    public function getPlayer2( )
    {
        return $this->player2()->first();
    }

    public function sets()
    {
        return $this->hasMany('App\Set');
        //return $this->hasMany('App\Set');
    }

    public static function calculateWinner( $sets )
    {
        $winner = 0;
        $p1 = 0;
        $p2 = 0;

        foreach( $sets AS $set )
        {

            /*
            $set = [
                'score1' => $score['set'][0],
                'score2' => $score['set'][1]
            ];
            */

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
                return 1;
            }
            if( $p2 >= 2 )
            {
                return 2;
            }
        }

        return $winner;
    }

    public function getScore(  )
    {
        $sets = $this->sets()->get();


        $diff = $this->score1 - $this->score2;
        switch (true) {
            case $diff < 0 :
                $matchScore = self::RESULT_LOSS;
                break;
            case $diff > 0 :
                $matchScore = self::RESULT_WIN;
                break;
            default :
                $matchScore = self::RESULT_DRAW;
                break;
        }
        return (float)$matchScore;


    }



    public static function createNewGame($players, $scoreData )
    {

        $sets = [];

        foreach( $scoreData AS $score )
        {
            $set = [
                'score1' => $score['set'][0],
                'score2' => $score['set'][1]
            ];

            $sets[] = $set;
        }

        $winner = self::calculateWinner( $sets );


        if( $winner < 0 )
        {
            return ['error' => 'No winner in match. Plz play moar!' ];
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

        $match = Match::create([
            'winner'        => $winner,
            'status'        => 'confirmed',
            'player1_id'    => $players[0]->id,
            'player2_id'    => $players[1]->id,
            'rating_adjustment_player1' => 0,
            'rating_adjustment_player2' => 0,
        ]);

        /*
        $match->player1()->save( $players[0] );
        $match->player2()->save( $players[1] );
        */
        /*
        // Add players with relation to match
        foreach($players AS $player)
        {
            $match->players()->attach( $player->id );
        }
        */


        // Add sets with relation to match
        foreach($sets AS $set)
        {
            print_r($set);
            $match->sets()->create( $set );
            //$match->sets()->create( $set );
        }


        //$data = $match->toArray();

        /*
        $elo = new Elo( );
        $new_rankings = $elo->calculateGame( $match );

        $players[0]->adjustRating ( $new_rankings['player1'] );
        $players[1]->adjustRating ( $new_rankings['player2'] );

        $match->rating_adjustment_player1 = $new_rankings['player1'];
        $match->rating_adjustment_player2 = $new_rankings['player2'];
        */


        $glicko = new Glicko2();


        $match = $glicko->calculateMatch($match, $winner);

        die('asdasd');
        $match->save();



        //$new_rankings = self::calculateScore( $players[0], $players[1], $sets );


        //print_r($new_rankings );




        // Queue recalculation of player stats
        foreach($players AS $player)
        {
            Queue::push(new RefreshPlayerStats( $player ));
        }

        return $match;
    }

    public static function calculateScore( $player1, $player2, $sets )
    {

        $glicko = new Glicko2();

        /*
        $player1 = new Player(1700, 250, 0.05);
        $player2 = new Player();
        */

        /*
        $match = new Match($player1, $player2, 1, 0);
        $glicko->calculateMatch($match);

        $match = new Match($player1, $player2, 3, 2);
        $glicko->calculateMatch($match);
        */
    }

    public static function create(array $attributes = [])
    {
        return parent::create($attributes);
    }
}
