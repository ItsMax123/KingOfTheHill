<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\tasks;

use Max\KingOfTheHill\Game;
use pocketmine\scheduler\Task;

class GameTask extends Task {
    public Game $game;

    public function __construct(Game $game) {
        $this->game = $game;
    }

    public function onRun(): void {
        $king = $this->game->getKing();
        if (is_null($king)) {
            $this->game->startCaptureRandom();
            return;
        }

        $kingPlayer = $king->getPlayer();
        if (!$kingPlayer->isOnline() || !$this->game->getHill()->isInside($kingPlayer->getPosition())) {
            $this->game->stopCapture();
            $this->game->startCaptureRandom();
            return;
        }

        if ($king->isCapturing()) {
            $this->game->update();
        } else {
            $this->game->win();
            Game::stopGame();
        }
    }
}