<?php

namespace Zelenin\Glicko2;

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
    public function calculateMatch(Match $match)
    {
        $player1 = clone $match->getPlayer1();
        $player2 = clone $match->getPlayer2();

        $score = $match->getScore();

        $r1 = $this->calculatePlayer($player1, $player2, $score);
        $r2 = $this->calculatePlayer($player2, $player1, (1 - $score));

        $match->getPlayer1()->loadFromCalculationResult($r1);
        $match->getPlayer2()->loadFromCalculationResult($r2);
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
        $phi = $player1->getPhi();
        $mu = $player1->getMu();
        $sigma = $player1->getSigma();

        $phiJ = $player2->getPhi();
        $muJ = $player2->getMu();

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
        return 1 / sqrt(1 + 3 * pow($phiJ, 2) / pow(3.14, 2));
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
        $tau = 0.5;

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
