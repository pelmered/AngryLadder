<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Player extends Model
{
    protected $fillable = array('name', 'ranking');
    //protected $appends = array('banner');

    /**
     * Plugin path
     * @param string $path Plugin path
     * @param null $tag Optionally, a plugin tag
     */
    function load( $path, $tag = null )
    {

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
