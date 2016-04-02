<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Player extends Model
{
    protected $fillable = array('name', 'slack_id', 'slack_name', 'ranking');

    public static function getByIDorSlackID( $id )
    {

        $player = self::where(function($query) use ($id) {
            $query->where('id',         '=', $id)
                ->orWhere('slack_id',   '=', $id)
                ->orWhere('slack_name', '=', $id);
            })
            ->orderBy('name', 'desc')
            //->take(1)
            ->get();

        return $player;
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
