<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\events\capture;

use Max\KingOfTheHill\events\HillEvent;
use Max\KingOfTheHill\Hill;
use Max\KingOfTheHill\King;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class CaptureStopEvent extends HillEvent implements Cancellable {
    use CancellableTrait;

    protected King $king;

    public function __construct(Hill $hill, King $king) {
        $this->hill = $hill;
        $this->king = $king;
    }

    public function getKing(): King {
        return $this->king;
    }
}