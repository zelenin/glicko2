# Glicko2

A PHP implementation of [Glicko2 rating system](http://www.glicko.net/glicko.html)

## Installation

### Composer

The preferred way to install this extension is through [Composer](http://getcomposer.org/).

Either run

```
php composer.phar require zelenin/glicko2 "~1.0.0"
```

or add

```
"zelenin/glicko2": "~1.0.0"
```

to the require section of your ```composer.json```

## Usage

Create two players with current ratings:

```php
use Zelenin\Glicko2\Glicko2;
use Zelenin\Glicko2\Match;
use Zelenin\Glicko2\MatchCollection;
use Zelenin\Glicko2\Player;

$glicko = new Glicko2();

$player1 = new Player(1700, 250, 0.05);
$player2 = new Player();

$match = new Match($player1, $player2, 1, 0);
$glicko->calculateMatch($match);

$match = new Match($player1, $player2, 3, 2);
$glicko->calculateMatch($match);

// or

$matchCollection = new MatchCollection();
$matchCollection->addMatch(new Match($player1, $player2, 1, 0));
$matchCollection->addMatch(new Match($player1, $player2, 3, 2));
$glicko->calculateMatches($matchCollection);

$newPlayer1R = $player1->getR();
$newPlayer2R = $player2->getR();
```

## Author

[Aleksandr Zelenin](https://github.com/zelenin/), e-mail: [aleksandr@zelenin.me](mailto:aleksandr@zelenin.me)
