<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\events\capture;

use Max\KingOfTheHill\events\GameEvent;
use Max\KingOfTheHill\Game;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class CaptureStartEvent extends GameEvent implements Cancellable {
    use CancellableTrait;

    protected Player $player;
    protected int $time;

    public function __construct(Game $game, Player $player, int $time) {
        $this->game = $game;
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