<?php

namespace App;


use DB;

use Cache;
#use Illuminate\Database\DB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Player extends Model
{
    protected $fillable = array('name', 'email', 'avatar_url', 'slack_id', 'slack_name', 'ranking', 'added_from');

    protected $dates = ['deleted_at'];

    public static function getByIDorSlackID( $id )
    {

        if( is_array($id) )
        {
            $keys = ['id', 'slack_id', 'slack_name'];

            $found = false;

            foreach($keys AS $key)
            {
                if( isset($id[$key]) )
                {
                    $id = $id[$key];

                    $found = true;

                    break;
                }
            }

            if( !$found )
            {
                return false;
            }
        }

        $player = self::where(function($query) use ($id) {

            $query->where('id',         '=', $id)
                ->orWhere('slack_id',   '=', $id)
                ->orWhere('slack_name', '=', $id);
            })
            ->orderBy('name', 'desc')
            //->take(1)
            ->get()->first();

        return $player;
    }
    
    public static function getByAny( $data )
    {
        $allowed_keys = ['id', 'slack_id', 'slack_name', 'email'];

        $data = array_filter($data, function($e) use ($allowed_keys) {
            return in_array($e, $allowed_keys);
        }, ARRAY_FILTER_USE_KEY);

        $player = self::where(function($query) use ($data) {
                foreach( $data AS $filed_key => $field_value )
                {
                    $query->orWhere($filed_key, '=', $field_value);
                }

            })
            ->orderBy('name', 'desc')
            //->take(1)
            ->get()->first();

        return $player;
    }

    public static function getPlayersFromJSON($players, $createIfNotExist = false)
    {
        $_players = [];

        foreach($players AS $_player)
        {
            if( is_numeric($_player) && intval($_player) > 0)
            {
                $player = self::find( $_player );
            }
            else if( is_array($_player) )
            {
                $player = self::getByIDorSlackID($_player);
            }

            if( empty($player) )
            {
                if( $createIfNotExist )
                {
                    $playerObj = new Player;

                    $allowed_fields = ['name', 'email', 'slack_id', 'slack_name', 'avatar_url'];



                    foreach( $_player AS $key => $value)
                    {
                        if( in_array($key, $allowed_fields))
                        {
                            $playerObj->{$key} = $value;
                        }
                    }

                    $playerObj->rating = 1000;
                    $playerObj->rating_weekly = 1000;
                    $playerObj->added_from = 'slackbot';

                    $playerObj->save();


                    $_players[] = $playerObj;
                }
                else
                {
                    return ['error' => $_player];
                }
            }
            else{
                $_players[] = $player;
            }


        }

        return $_players;
    }

    public static function getRank( $playerId )
    {


        $result = DB::select( DB::raw( "
            SELECT rating_weekly,rating,
                (
                    SELECT COUNT(1) AS num
                    FROM players
                    WHERE players.rating_weekly > s1.rating_weekly
                      AND players.rating_weekly != 1000
                ) + 1 AS weekly,
                (
                    SELECT COUNT(1) AS num
                    FROM players
                    WHERE players.rating > s1.rating
                      AND players.rating != 1000
                ) + 1 AS alltime
                FROM players AS s1
	            WHERE ID = :playerId
            ORDER BY rating desc
        " ), [
            'playerId' => $playerId
        ] );


        if( isset( $result[0]->alltime )) {
            $rank = new Rank();

            if ($result[0]->rating_weekly == 1000)
            {

            }
            if ($result[0]->rating == 1000)
            {

            }
            $rank->weekly = ($result[0]->rating_weekly == 1000 ? '-' : $result[0]->weekly) ;
            $rank->allTime = ($result[0]->rating == 1000 ? '-' : $result[0]->alltime) ;

            return $rank;


            return $result[0]->rank;
        }

        return false;
    }


    public static function getStats( $playerId, $useCache = true )
    {


        $stats = [
            'wins',
            'loses',
            'set_wins',
            'set_loses',
            'total_points',
            'total_points_against',
            'total_points_diff',
        ];

        $stats = new PlayerStats();

        $stats->games_played            = 0;
        $stats->wins                    = 0;
        $stats->loses                   = 0;
        $stats->win_percent             = 0;
        $stats->set_wins                = 0;
        $stats->set_loses               = 0;
        $stats->win_percent             = 0;
        $stats->total_points            = 0;
        $stats->total_points_against    = 0;
        $stats->total_points_diff       = 0;
        $stats->updated                 = null;

        if( $useCache )
        {
            $refresh = false;

            $attributes = $stats->getAttributes();

            foreach( $attributes AS $stat_key => $value )
            {
                $cache = Cache::get('stats_player_'.$playerId.'_'.$stat_key, false);

                if( $cache )
                {
                    $stats->$stat_key = $cache;
                }
                else
                {
                    $refresh = true;
                    break;
                }

            }

            if( !$refresh )
            {
                return $stats;
            }


        }

        //TODO: Needs rewrite
        /*
        $games = Game::with('players', 'sets')
            ->orderBy('updated_at', 'desc')
            ->get();
        */




        $games = Player::find($playerId)->games()->get();


        foreach( $games AS $game )
        {
            $stats->games_played++;

            $game_array = Game::with('players', 'sets')->find($game->id)->toArray();

            $player_num = ( $game_array['players'][0]['id'] == $playerId ? 1 : 2 );


            if( $game_array['winner'] == $player_num )
            {
                $stats->wins++;
            }
            else
            {
                $stats->loses++;
            }

            foreach( $game_array['sets'] AS $set )
            {
                if(
                    //Win as player 1
                    ($player_num == 1 && $set['score1'] > $set['score2']) ||
                    //Win as player 2
                    ($player_num == 2 && $set['score1'] < $set['score2'])
                )
                {
                    $stats->set_wins++;
                    $stats->total_points += $set['score1'];
                    $stats->total_points_against += $set['score2'];
                }
                //else, it's a loss
                else
                {
                    $stats->set_loses++;
                    $stats->total_points += $set['score2'];
                    $stats->total_points_against += $set['score1'];
                }


            }

        }

        $stats->total_points_diff = $stats->total_points - $stats->total_points_against;


        if( $stats->wins > 0 )
        {
            $stats->win_percent =  round( 100 * $stats->wins / $stats->games_played, 2);
        }
        if( $stats->wins > 0 )
        {
            $stats->set_wins_percent = round( 100 * $stats->set_wins / ( $stats->set_wins + $stats->set_loses ), 2);
        }

        $stats->updated = time();


        return $stats;
    }


    public function adjustRating( $adjustment )
    {
        $this->rating += $adjustment;
        $this->rating_weekly += $adjustment;

        $this->save();
    }


    public function games()
    {
        return $this->belongsToMany('App\Game');
    }

    public function rank()
    {
        return $this->belongsToMany('App\Rank');
    }

    public function stats()
    {
        return $this->belongsToMany('App\PlayerStats');
    }

}
