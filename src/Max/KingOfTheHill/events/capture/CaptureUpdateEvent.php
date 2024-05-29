<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\events\capture;

use Max\KingOfTheHill\events\HillEvent;
use Max\KingOfTheHill\Hill;
use Max\KingOfTheHill\King;

class CaptureUpdateEvent extends HillEvent {
    protected King $king;

    public function __construct(Hill $hill, King $king) {
        $this->hill = $hill;
        $this->king = $king;
    }

    public function getKing(): King {
        return $this->king;
    }
}