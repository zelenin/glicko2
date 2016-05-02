<?php

namespace Zelenin\Glicko2;

final class CalculationResult
{
    /**
     * @var float
     */
    private $mu;

    /**
     * @var float
     */
    private $phi;

    /**
     * @var float
     */
    private $sigma;

    /**
     * @param float $mu
     * @param float $phi
     * @param float $sigma
     */
    public function __construct($mu, $phi, $sigma)
    {
        $this->mu = $mu;
        $this->phi = $phi;
        $this->sigma = $sigma;
    }

    /**
     * @return float
     */
    public function getMu()
    {
        return $this->mu;
    }

    /**
     * @return float
     */
    public function getPhi()
    {
        return $this->phi;
    }

    /**
     * @return float
     */
    public function getSigma()
    {
        return $this->sigma;
    }
}
