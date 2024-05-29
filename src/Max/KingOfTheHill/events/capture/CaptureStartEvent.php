<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\events\capture;

use Max\KingOfTheHill\events\HillEvent;
use Max\KingOfTheHill\Hill;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class CaptureStartEvent extends HillEvent implements Cancellable {
    use CancellableTrait;

    protected Player $player;
    protected int $time;

    public function __construct(Hill $hill, Player $player, int $time) {
        $this->hill = $hill;
        $this->player = $player;
        $this->time = $time;
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function setPlayer(Player $player): void {
        $this->player = $player;
    }

    public function getTime(): int {
        return $this->time;
    }

    public function setTime(int $time): void {
        $this->time = $time;
    }
}