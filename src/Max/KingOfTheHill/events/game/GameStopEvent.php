<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\events\game;

use Max\KingOfTheHill\events\HillEvent;
use Max\KingOfTheHill\Hill;

class GameStopEvent extends HillEvent {
    public function __construct(Hill $hill) {
        $this->hill = $hill;
    }
}