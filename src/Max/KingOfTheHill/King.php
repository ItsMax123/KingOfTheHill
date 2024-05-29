<?php

declare(strict_types=1);

namespace Max\KingOfTheHill;

use pocketmine\player\Player;
use pocketmine\Server;

class King {
    public Player $player;
    public int $captureEndTick;

    public function __construct(Player $player, int $time) {
        $this->player = $player;
        $this->captureEndTick = Server::getInstance()->getTick() + $time;
    }

    public function getPlayer(): ?Player {
        return $this->player;
    }

    public function getCaptureEndTick(): int {
        return $this->captureEndTick;
    }

    public function isCapturing(): bool {
        return $this->captureEndTick > Server::getInstance()->getTick();
    }

    public function getCaptureTicksLeft(): int {
        return $this->captureEndTick - Server::getInstance()->getTick();
    }
}