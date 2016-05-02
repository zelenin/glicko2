<?php

namespace Zelenin\Glicko2\Test;

use PHPUnit_Framework_TestCase;
use Zelenin\Glicko2\Glicko2;
use Zelenin\Glicko2\Match;
use Zelenin\Glicko2\MatchCollection;
use Zelenin\Glicko2\Player;

final class Glicko2Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var Glicko2
     */
    private $glicko;

    public function setUp()
    {
        $this->glicko = new Glicko2();
        parent::setUp();
    }

    public function testDefaultPlayer()
    {
        $player = new Player();

        $this->assertEquals(Player::DEFAULT_R, $player->getR());
        $this->assertEquals(Player::DEFAULT_RD, $player->getRd());
        $this->assertEquals(Player::DEFAULT_SIGMA, $player->getSigma());
    }

    public function testCustomPlayer()
    {
        $r = 1700;
        $rd = 300;
        $sigma = 0.04;

        $player = new Player($r, $rd, $sigma);

        $this->assertEquals($r, $player->getR());
        $this->assertEquals($rd, $player->getRd());
        $this->assertEquals($sigma, $player->getSigma());
    }

    public function testCalculateMatch()
    {
        $player1 = new Player(1500, 200, 0.06);
        $player2 = new Player(1400, 30, 0.06);

        $match = new Match($player1, $player2, 1, 0);
        $this->glicko->calculateMatch($match);

        $this->assertEquals(1563.564, $this->round($player1->getR()));
        $this->assertEquals(175.403, $this->round($player1->getRd()));
        $this->assertEquals(0.06, $this->round($player1->getSigma()));

        $this->assertEquals(1398.144, $this->round($player2->getR()));
        $this->assertEquals(31.67, $this->round($player2->getRd()));
        $this->assertEquals(0.06, $this->round($player2->getSigma()));
    }

    public function testCalculateMatchCollection()
    {
        $player1 = new Player(1500, 200, 0.06);
        $player2 = new Player(1400, 30, 0.06);

        $player3 = clone $player1;
        $player4 = clone $player2;

        $match = new Match($player1, $player2, 1, 0);
        $this->glicko->calculateMatch($match);
        $match = new Match($player1, $player2, 1, 0);
        $this->glicko->calculateMatch($match);

        $matchCollection = new MatchCollection();
        $matchCollection->addMatch(new Match($player3, $player4, 1, 0));
        $matchCollection->addMatch(new Match($player3, $player4, 1, 0));
        $this->glicko->calculateMatches($matchCollection);

        $this->assertEquals($this->round($player1->getR()), $this->round($player3->getR()));
        $this->assertEquals($this->round($player2->getR()), $this->round($player4->getR()));
        $this->assertEquals($this->round($player1->getRd()), $this->round($player3->getRd()));
        $this->assertEquals($this->round($player2->getRd()), $this->round($player4->getRd()));
        $this->assertEquals($this->round($player1->getSigma()), $this->round($player3->getSigma()));
        $this->assertEquals($this->round($player2->getSigma()), $this->round($player4->getSigma()));
    }

    /**
     * For different platforms compatibility
     *
     * @param float $value
     *
     * @return float
     */
    private function round($value)
    {
        return round($value, 3);
    }
}
