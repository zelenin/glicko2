<?php

namespace Zelenin\Glicko2;

final class Player
{
    const CONVERT = 173.7178;

    const DEFAULT_R = 1500;
    const DEFAULT_RD = 350;
    const DEFAULT_SIGMA = 0.06;

    /**
     * A rating r
     *
     * @var float
     */
    private $r;

    /** A rating μ
     *
     * @var float
     */
    private $mu;

    /**
     * A rating deviation RD
     *
     * @var float
     */
    private $RD;

    /**
     * A rating deviation φ
     *
     * @var float
     */
    private $phi;

    /**
     * A rating volatility σ
     *
     * @var float
     */
    private $sigma;

    /**
     * @param float $rating
     * @param float $RD
     * @param float $sigma
     */
    public function __construct($r = self::DEFAULT_R, $RD = self::DEFAULT_RD, $sigma = self::DEFAULT_SIGMA)
    {
        $this->setR($r);
        $this->setRD($RD);
        $this->setSigma($sigma);
    }

    /**
     * @param float $r
     */
    private function setR($r)
    {
        $this->r = $r;
        $this->mu = ($this->r - self::DEFAULT_R) / self::CONVERT;
    }

    /**
     * @param float $mu
     */
    private function setMu($mu)
    {
        $this->mu = $mu;
        $this->r = $this->mu * self::CONVERT + self::DEFAULT_R;
    }

    /**
     * @param float $RD
     */
    private function setRD($RD)
    {
        $this->RD = $RD;
        $this->phi = $this->RD / self::CONVERT;
    }

    /**
     * @param float $phi
     */
    private function setPhi($phi)
    {
        $this->phi = $phi;
        $this->RD = $this->phi * self::CONVERT;
    }

    /**
     * @param float $sigma
     */
    private function setSigma($sigma)
    {
        $this->sigma = $sigma;
    }

    /**
     * @return float
     */
    public function getR()
    {
        return $this->r;
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
    public function getRd()
    {
        return $this->RD;
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

    /**
     * @param CalculationResult $calculationResult
     */
    public function loadFromCalculationResult(CalculationResult $calculationResult)
    {
        $this->setMu($calculationResult->getMu());
        $this->setPhi($calculationResult->getPhi());
        $this->setSigma($calculationResult->getSigma());
    }
}
