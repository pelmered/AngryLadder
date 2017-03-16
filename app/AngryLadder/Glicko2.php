<?php

namespace App\AngryLadder;

use \App\Player;
use \App\PlayerRating;
use App\Match;
use Illuminate\Support\Facades\DB;

final class Glicko2
{
    /**
     * system constant τ
     *
     * @var float
     */
    private $tau;

    /**
     * @param float $tau
     */
    public function __construct($tau = 0.5)
    {
        $this->tau = $tau;
    }

    /**
     * @param MatchCollection $matchCollection
     */
    public function calculateMatches(MatchCollection $matchCollection)
    {
        foreach ($matchCollection->getMatches() as $match) {
            $this->calculateMatch($match);
        }
    }

    /**
     * @param Match $match
     */
    //public function calculateMatch(Match $match)
    public function calculateMatch(Match $match, $winner)
    {

        DB::enableQueryLog();

        //($match->getPlayer1());
        //dd(DB::getQueryLog());

        $player1 = clone $match->getPlayer1();
        $player2 = clone $match->getPlayer2();

        $calculationResult1 = $this->calculatePlayer($player1, $player2, $winner === 1 ? 1 : 0);
        $calculationResult2 = $this->calculatePlayer($player2, $player1, $winner === 2 ? 1 : 0);

        /*
        print_r($calculationResult1);
        print_r($calculationResult2);
        */

        $match->rating_adjustment_player1 = $match->getPlayer1()->getRating()->setCalculationResult($calculationResult1);
        $match->rating_adjustment_player2 = $match->getPlayer2()->getRating()->setCalculationResult($calculationResult2);

        //$match->save();

        return $match;
    }

    /**
     * @param Player $player1
     * @param Player $player2
     * @param int $score
     *
     * @return CalculationResult
     */
    private function calculatePlayer(Player $player1, Player $player2, $score)
    {
        $player1Rating = $player1->getRating();
        $player2Rating = $player2->getRating();

        $phi = $player1Rating->getRatingDeviationPhi();
        $mu = $player1Rating->getRatingMu();
        $sigma = $player1Rating->getRatingVolatility();

        $phiJ = $player2Rating->getRatingDeviationPhi();
        $muJ = $player2Rating->getRatingMu();



        $v = $this->v($phiJ, $mu, $muJ);
        $delta = $this->delta($phiJ, $mu, $muJ, $score);
        $sigmaP = $this->sigmaP($delta, $sigma, $phi, $phiJ, $mu, $muJ);
        $phiS = $this->phiS($phi, $sigmaP);
        $phiP = $this->phiP($phiS, $v);
        $muP = $this->muP($mu, $muJ, $phiP, $phiJ, $score);

        return new CalculationResult($muP, $phiP, $sigmaP);
    }

    /**
     * @param float $phiJ
     * @param float $mu
     * @param float $muJ
     *
     * @return float
     */
    private function v($phiJ, $mu, $muJ)
    {
        $g = $this->g($phiJ);
        $E = $this->E($mu, $muJ, $phiJ);
        return 1 / ($g * $g * $E * (1 - $E));
    }

    /**
     * @param float $phiJ
     *
     * @return float
     */
    private function g($phiJ)
    {
        return 1 / sqrt(1 + 3 * pow($phiJ, 2) / pow(M_PI, 2));
    }

    /**
     * @param float $mu
     * @param float $muJ
     * @param float $phiJ
     *
     * @return float
     */
    private function E($mu, $muJ, $phiJ)
    {
        return 1 / (1 + exp(-$this->g($phiJ) * ($mu - $muJ)));
    }

    /**
     * @param float $phiJ
     * @param float $mu
     * @param float $muJ
     * @param float $score
     *
     * @return float
     */
    private function delta($phiJ, $mu, $muJ, $score)
    {
        return $this->v($phiJ, $mu, $muJ) * $this->g($phiJ) * ($score - $this->E($mu, $muJ, $phiJ));
    }

    /**
     * @param float $delta
     * @param float $sigma
     * @param float $phi
     * @param float $phiJ
     * @param float $mu
     * @param float $muJ
     *
     * @return float
     */
    private function sigmaP($delta, $sigma, $phi, $phiJ, $mu, $muJ)
    {
        $A = $a = log(pow($sigma, 2));
        $fX = function ($x, $delta, $phi, $v, $a, $tau) {
            return ((exp($x) * (pow($delta, 2) - pow($phi, 2) - $v - exp($x))) / (2 * pow((pow($phi, 2) + $v + exp($x)), 2))) - (($x - $a) / pow($tau, 2));
        };
        $epsilon = 0.000001;
        $v = $this->v($phiJ, $mu, $muJ);
        $tau = $this->tau;

        if (pow($delta, 2) > (pow($phi, 2) + $v)) {
            $B = log(pow($delta, 2) - pow($phi, 2) - $v);
        } else {
            $k = 1;
            while ($fX($a - $k * $tau, $delta, $phi, $v, $a, $tau) < 0) {
                $k++;
            }
            $B = $a - $k * $tau;
        }

        $fA = $fX($A, $delta, $phi, $v, $a, $tau);
        $fB = $fX($B, $delta, $phi, $v, $a, $tau);

        while (abs($B - $A) > $epsilon) {
            $C = $A + $fA * ($A - $B) / ($fB - $fA);
            $fC = $fX($C, $delta, $phi, $v, $a, $tau);
            if (($fC * $fB) < 0) {
                $A = $B;
                $fA = $fB;
            } else {
                $fA = $fA / 2;
            }
            $B = $C;
            $fB = $fC;
        }

        return exp($A / 2);
    }

    /**
     * @param float $phi
     * @param float $sigmaP
     *
     * @return float
     */
    private function phiS($phi, $sigmaP)
    {
        return sqrt(pow($phi, 2) + pow($sigmaP, 2));
    }

    /**
     * @param float $phiS
     * @param float $v
     *
     * @return float
     */
    private function phiP($phiS, $v)
    {
        var_dump($phiS);
        var_dump($v);
        var_dump(pow($phiS, 2));
        var_dump(sqrt(1 / pow($phiS, 2) + 1 / $v));


        return 1 / sqrt(1 / pow($phiS, 2) + 1 / $v);
    }

    /**
     * @param float $mu
     * @param float $muJ
     * @param float $phiP
     * @param float $phiJ
     * @param int $score
     *
     * @return float
     */
    private function muP($mu, $muJ, $phiP, $phiJ, $score)
    {
        return $mu + pow($phiP, 2) * $this->g($phiJ) * ($score - $this->E($mu, $muJ, $phiJ));
    }
}
