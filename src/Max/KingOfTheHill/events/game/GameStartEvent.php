<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\events\game;

use Max\KingOfTheHill\Hill;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;

class GameStartEvent extends Event implements Cancellable {
    use CancellableTrait;

    private Hill $hill;

    public function __construct(Hill $hill) {
        $this->hill = $hill;
    }

    public function getHill(): Hill {
        return $this->hill;
    }

    public function setHill(Hill $hill): void {
        $this->hill = $hill;
    }
}