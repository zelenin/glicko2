<?php

namespace Zelenin\Glicko2;

final class Match
{
    /**
     * @var Player
     */
    private $player1;

    /**
     * @var Player
     */
    private $player2;

    /**
     * @var float
     */
    private $score1;

    /**
     * @var float
     */
    private $score2;

    const RESULT_WIN = 1;
    const RESULT_DRAW = 0.5;
    const RESULT_LOSS = 0;

    /**
     * @param Player $player1
     * @param Player $player2
     * @param float $score1
     * @param float $score2
     */
    public function __construct(Player $player1, Player $player2, $score1, $score2)
    {
        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->score1 = (float)$score1;
        $this->score2 = (float)$score2;
    }

    /**
     * @return float
     */
    public function getScore()
    {
        $diff = $this->score1 - $this->score2;
        switch (true) {
            case $diff < 0 :
                $matchScore = self::RESULT_LOSS;
                break;
            case $diff > 0 :
                $matchScore = self::RESULT_WIN;
                break;
            default :
                $matchScore = self::RESULT_DRAW;
                break;
        }
        return (float)$matchScore;
    }

    /**
     * @return Player
     */
    public function getPlayer1()
    {
        return $this->player1;
    }

    /**
     * @return Player
     */
    public function getPlayer2()
    {
        return $this->player2;
    }
}
