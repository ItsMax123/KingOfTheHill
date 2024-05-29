<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\tasks;

use Max\KingOfTheHill\events\capture\CaptureStartEvent;
use Max\KingOfTheHill\events\capture\CaptureStopEvent;
use Max\KingOfTheHill\events\capture\CaptureUpdateEvent;
use Max\KingOfTheHill\events\capture\CaptureWinEvent;
use Max\KingOfTheHill\Hill;
use Max\KingOfTheHill\King;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class GameTask extends Task {
    public Hill $hill;
    public ?King $king = null;

    public function __construct(Hill $hill) {
        $this->hill = $hill;
    }

    public function onRun(): void {
        if (is_null($this->king)) {
            $this->captureStart();
            return;
        }

        $kingPlayer = $this->king->getPlayer();
        if (!$kingPlayer->isOnline() || !$this->hill->isInside($kingPlayer->getPosition())) {
            $captureStopEvent = new CaptureStopEvent($this->hill, $this->king);
            $captureStopEvent->call();
            if ($captureStopEvent->isCancelled()) return;
            $this->king = null;
            $this->captureStart();
            return;
        }

        if ($this->king->isCapturing()) {
            $captureUpdateEvent = new CaptureUpdateEvent($this->hill, $this->king);
            $captureUpdateEvent->call();
        } else {
            $captureWinEvent = new CaptureWinEvent($this->hill, $kingPlayer, $this->hill->getRewards());
            $captureWinEvent->call();
            if ($captureWinEvent->isCancelled()) return;
            $server = KingOfTheHill::getInstance()->getServer();
            $consoleCommandSender = new ConsoleCommandSender($server, $server->getLanguage());
            $playerName = $captureWinEvent->getPlayer()->getName();
            foreach ($captureWinEvent->getRewards() as $command) {
                $server->dispatchCommand($consoleCommandSender, str_replace("{PLAYER}", $playerName, $command));
            }
            $server->broadcastMessage(str_replace(
                ["{PLAYER}", "{HILL}"],
                [$playerName, $this->hill->getName()],
                KingOfTheHill::getInstance()->getMessage("broadcast.win")
            ));
            KingOfTheHill::getInstance()->stopGame();
        }
    }

    public function captureStart(): void {
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        shuffle($onlinePlayers);
        foreach ($onlinePlayers as $player) {
            if ($this->hill->isInside($player->getPosition())) {
                $captureStartEvent = new CaptureStartEvent($this->hill, $player, $this->hill->getTime());
                $captureStartEvent->call();
                if ($captureStartEvent->isCancelled()) return;
                $this->king = new King($captureStartEvent->getPlayer(), $captureStartEvent->getTime());
            }
        }
    }
}