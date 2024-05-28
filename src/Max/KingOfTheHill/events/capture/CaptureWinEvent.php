<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\events\capture;

use Max\KingOfTheHill\events\GameEvent;
use Max\KingOfTheHill\Game;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class CaptureWinEvent extends GameEvent implements Cancellable {
    use CancellableTrait;

    protected array $rewards;

    public function __construct(Game $game, array $rewards) {
        $this->game = $game;
        $this->rewards = $rewards;
    }

    public function getRewards(): array {
        return $this->rewards;
    }

    public function setRewards(array $rewards): void {
        $this->rewards = $rewards;
    }
}