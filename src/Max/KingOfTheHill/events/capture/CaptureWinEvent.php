<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\events\capture;

use Max\KingOfTheHill\events\HillEvent;
use Max\KingOfTheHill\Hill;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class CaptureWinEvent extends HillEvent implements Cancellable {
    use CancellableTrait;

    protected Player $player;
    protected array $rewards;

    public function __construct(Hill $hill, Player $player, array $rewards) {
        $this->hill = $hill;
        $this->player = $player;
        $this->rewards = $rewards;
    }

    public function getPlayer(): Player {
        return $this->player;
    }

    public function setPlayer(Player $player): void {
        $this->player = $player;
    }

    public function getRewards(): array {
        return $this->rewards;
    }

    public function setRewards(array $rewards): void {
        $this->rewards = $rewards;
    }
}