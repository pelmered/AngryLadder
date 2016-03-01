<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Game extends Model
{
    protected $fillable = array('player1', 'player2', 'score1', 'score2');
    //protected $appends = array('banner');

    /**
     * Plugin path
     * @param string $path Plugin path
     * @param null $tag Optionally, a plugin tag
     */
    function load( $path, $tag = null )
    {
        /*
        //Create filesystem component
        $this->fs = null;//new Filesystem();

        //If not absolute path, treat it as a slug
        if(substr($path, 0, 1) !== '/')
            $this->path = Config::get('plugin_path') . '/' . $path;
        else
            $this->path = $path;

        //Calculate plugin slug based on path
        $exploded_path = explode('/', $path);
        $this->slug = $exploded_path[sizeof($exploded_path)-1];

        //Set cache adapter if one is given
        $this->cache = Cache::getAdapter();

        //Init DB
        $this->db = DB::getInstance();

        //Check if object exists in DB
        $this->id = $this->db->getPluginId($this->slug);

        //If object exists in DB, load it from DB.
        if($this->id !== null) {
            $this->plugin_data = $this->db->getPluginDataByID($this->id);
        }
        //Else, grab the data from the API and then save
        else {

            //Populate plugin data
            $wporg_data = $this->getWpOrgData();

            //Save only if WPorg data actually gives a proper response
            if(is_array($wporg_data)) {
                $this->id = $this->db->updatePluginData(null, $wporg_data);
            }
            //Else mark plugin as possibly broken
            else {
                //$this->db->save('plugins', array('asfasf' => rand(0,200)), $this->id);
            }
        }
        */

        //$this->db->save('plugins', array('name' => rand(0,200)), $this->id);
    }

    /*
    public function tag()
    {
        return $this->hasMany('App\Tag');
    }


    public function PluginMeta()
    {
        return $this->hasMany('App\PluginMeta');
    }
    */


    public function player1()
    {
        return $this->belongsTo('App\Player', 'player1');
    }
    public function player2()
    {
        return $this->belongsTo('App\Player', 'player2');
    }
}
