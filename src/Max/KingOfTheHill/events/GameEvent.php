<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\events;

use Max\KingOfTheHill\Game;
use pocketmine\event\Event;

abstract class GameEvent extends Event {
    protected Game $game;

    public function getGame(): Game {
        return $this->game;
    }
}