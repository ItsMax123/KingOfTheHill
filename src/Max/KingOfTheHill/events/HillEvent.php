<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\events;

use Max\KingOfTheHill\Hill;
use pocketmine\event\Event;

abstract class HillEvent extends Event {
    protected Hill $hill;

    public function getHill(): Hill {
        return $this->hill;
    }
}