<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\events\capture;

use Max\KingOfTheHill\events\GameEvent;
use Max\KingOfTheHill\Game;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class CaptureStopEvent extends GameEvent implements Cancellable {
    use CancellableTrait;

    public function __construct(Game $game) {
        $this->game = $game;
    }
}