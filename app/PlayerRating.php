<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

use App\AngryLadder\Elo;

use App\AngryLadder\CalculationResult;

class PlayerRating extends Model
{
    //protected $fillable = []; //array('ladder', 'rating');

    protected $config;

    public function __construct()
    {
        //$this->rank = $rank;

        $this->config = config('ladder.settings');

        parent::__construct();
    }

    public function players()
    {
        return $this->belongsTo('App\Player');
    }


    /**
     * @param float $r
     */
    public function setRating($r)
    {
        $this->rating = $r;
        $this->rating_mu = ($this->rating - $this->config['start_rating']) / $this->config['glicko2']['conversion_multiplier'];
        //$this->save();
    }

    /**
     * @param float $mu
     */
    public function setRatingMu($mu)
    {
        $this->rating_mu = $mu;

        $previousRating = $this->rating;

        $this->rating = $this->rating_mu * $this->config['glicko2']['conversion_multiplier'] + $this->config['start_rating'];

        //$this->save();

        return $this->rating - $previousRating;
    }

    /**
     * @param float $RD
     */
    public function setRatingDeviation($RD)
    {
        $this->rating_deviation = $RD;
        $this->rating_deviation_phi = $this->rating_deviation / $this->config['glicko2']['conversion_multiplier'];
    }

    /**
     * @param float $phi
     */
    public function setRatingDeviationPhi($phi)
    {
        $this->rating_deviation_phi = $phi;
        $this->rating_deviation = $this->rating_deviation_phi * $this->config['glicko2']['conversion_multiplier'];
    }

    /**
     * @param float $sigma
     */
    public function setVolatility($sigma)
    {
        $this->rating_volatility = $sigma;
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return float
     */
    public function getRatingMu()
    {
        return $this->rating_mu;
    }

    /**
     * @return float
     */
    public function getRatingDeviation()
    {
        return $this->rating_deviation;
    }

    /**
     * @return float
     */
    public function getRatingDeviationPhi()
    {
        return $this->rating_deviation_phi;
    }

    /**
     * @return float
     */
    public function getRatingVolatility()
    {
        return $this->rating_volatility;
    }

    /**
     * @param CalculationResult $calculationResult
     */
    public function setCalculationResult(CalculationResult $calculationResult)
    {
        $ratingDiff = $this->setRatingMu($calculationResult->getMu());
        $this->setRatingDeviationPhi($calculationResult->getPhi());
        $this->setVolatility($calculationResult->getVolatility());
        $this->save();

        return $ratingDiff;
    }

    public function init(  )
    {
        $ladderConfig = config('ladder.settings');

        $this->setRating($ladderConfig['start_rating']);
        $this->setRatingDeviation($ladderConfig['glicko2']['rating_deviation']);
        $this->setVolatility($ladderConfig['glicko2']['volatility']);
        //$this->save();
    }
}
