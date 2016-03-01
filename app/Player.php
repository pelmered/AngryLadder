<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Player extends Model
{
    protected $fillable = array('name');
    //protected $appends = array('banner');

    /**
     * Plugin path
     * @param string $path Plugin path
     * @param null $tag Optionally, a plugin tag
     */
    function load( $path, $tag = null )
    {

    }


    public function plugins()
    {
        return $this->belongsToMany('App\Game');
    }

}
