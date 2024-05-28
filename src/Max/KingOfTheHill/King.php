<?php

declare(strict_types=1);

namespace Max\KingOfTheHill;

use pocketmine\player\Player;
use pocketmine\Server;

class King {
    public Player $player;
    public int $captureEndTime;

    public function __construct(Player $player, int $time) {
        $this->player = $player;
        $this->captureEndTime = Server::getInstance()->getTick() + $time;
    }

    public function getPlayer(): ?Player {
        return $this->player;
    }

    public function getCaptureEndTime(): int {
        return $this->captureEndTime;
    }

    public function isCapturing(): bool {
        return $this->captureEndTime > Server::getInstance()->getTick();
    }

    public function getCaptureTicksLeft(): int {
        return $this->captureEndTime - Server::getInstance()->getTick();
    }
}