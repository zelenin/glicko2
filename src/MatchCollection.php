<?php

namespace Zelenin\Glicko2;

use ArrayIterator;
use ArrayObject;

final class MatchCollection
{
    /**
     * @var ArrayObject
     */
    private $matches;

    public function __construct()
    {
        $this->matches = new ArrayObject();
    }

    /**
     * @param Match $match
     */
    public function addMatch(Match $match)
    {
        $this->matches->append($match);
    }

    /**
     * @return ArrayIterator
     */
    public function getMatches()
    {
        return $this->matches->getIterator();
    }
}
