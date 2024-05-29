<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\events\game;

use Max\KingOfTheHill\events\HillEvent;
use Max\KingOfTheHill\Hill;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class GameStartEvent extends HillEvent implements Cancellable {
    use CancellableTrait;

    public function __construct(Hill $hill) {
        $this->hill = $hill;
    }

    public function setHill(Hill $hill): void {
        $this->hill = $hill;
    }
}