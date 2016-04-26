<?php

namespace App;


use DB;


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

    public static function getPlayersFromJSON($players)
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
                return ['error' => $_player];
            }

            $_players[] = $player;

        }

        return $_players;
    }

    public function adjustRating( $adjustment )
    {
        $this->rating += $adjustment;

        $this->save();
    }


    public function games()
    {
        return $this->belongsToMany('App\Game');
    }

}
