<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\events\game;

use Max\KingOfTheHill\events\GameEvent;
use Max\KingOfTheHill\Game;

class GameStopEvent extends GameEvent {
    public function __construct(Game $game) {
        $this->game = $game;
    }
}