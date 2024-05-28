<?php

declare(strict_types=1);

namespace Max\KingOfTheHill;

use Max\KingOfTheHill\events\capture\CaptureStartEvent;
use Max\KingOfTheHill\events\capture\CaptureStopEvent;
use Max\KingOfTheHill\events\capture\CaptureUpdateEvent;
use Max\KingOfTheHill\events\capture\CaptureWinEvent;
use Max\KingOfTheHill\events\game\GameStartEvent;
use Max\KingOfTheHill\events\game\GameStopEvent;
use Max\KingOfTheHill\tasks\GameTask;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Game {
    private static ?Game $game = null;
    private static TaskHandler $task;

    public Hill $hill;
    public ?King $king;

    public function __construct(Hill $hill) {
        $this->hill = $hill;
        $this->king = null;
    }

    public static function startGame(Hill $hill): bool {
        if (!is_null(self::$game)) return false;
        $gameStartEvent = new GameStartEvent($hill);
        $gameStartEvent->call();
        if ($gameStartEvent->isCancelled()) return false;
        $plugin = KingOfTheHill::getInstance();
        self::$game = new Game($gameStartEvent->getHill());
        self::$task = $plugin->getScheduler()->scheduleRepeatingTask(new GameTask(self::$game), $plugin->config->get("update-interval", 20));
        $plugin->getServer()->broadcastMessage(str_replace(
            ["{HILL}"],
            [$hill->getName()],
            TextFormat::colorize($plugin->messages->getNested("broadcast.game-started", "broadcast.game-started"))
        ));
        return true;
    }

    public static function stopGame(): void {
        if (is_null(self::$game)) return;
        $gameStopEvent = new GameStopEvent(self::$game);
        $gameStopEvent->call();
        $plugin = KingOfTheHill::getInstance();
        $plugin->getServer()->broadcastMessage(str_replace(
            ["{HILL}"],
            [self::$game->hill->getName()],
            TextFormat::colorize($plugin->messages->getNested("broadcast.game-stopped", "broadcast.game-stopped"))
        ));
        self::$task->cancel();
        self::$game = null;
    }

    public static function getGame(): ?Game {
        return self::$game;
    }

    public function getHill(): Hill {
        return $this->hill;
    }

    public function setHill(Hill $hill): void {
        $this->hill = $hill;
    }

    public function getKing(): ?King {
        return $this->king;
    }

    public function setKing(?King $king): void {
        $this->king = $king;
    }

    public function startCaptureRandom(): void {
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        shuffle($onlinePlayers);
        foreach ($onlinePlayers as $player) {
            if ($this->hill->isInside($player->getPosition())) {
                $this->startCapture($player);
            }
        }
    }

    public function startCapture(Player $player): void {
        $captureStartEvent = new CaptureStartEvent($this, $player, $this->hill->getTime());
        $captureStartEvent->call();
        if ($captureStartEvent->isCancelled()) return;
        $this->king = new King($captureStartEvent->getPlayer(), $captureStartEvent->getTime());
    }

    public function stopCapture(): void {
        $captureStopEvent = new CaptureStopEvent($this);
        $captureStopEvent->call();
        if ($captureStopEvent->isCancelled()) return;
        $this->king = null;
    }

    public function update(): void {
        $captureUpdateEvent = new CaptureUpdateEvent($this);
        $captureUpdateEvent->call();
    }

    public function win(): void {
        $captureWinEvent = new CaptureWinEvent($this, $this->hill->getRewards());
        $captureWinEvent->call();
        if ($captureWinEvent->isCancelled()) return;
        $server = KingOfTheHill::getInstance()->getServer();
        $consoleCommandSender = new ConsoleCommandSender($server, $server->getLanguage());
        $playerName = $this->king->getPlayer()->getName();
        foreach ($captureWinEvent->getRewards() as $command) {
            $server->dispatchCommand($consoleCommandSender, str_replace("{PLAYER}", $playerName, $command));
        }
        $plugin = KingOfTheHill::getInstance();
        $plugin->getServer()->broadcastMessage(str_replace(
            ["{PLAYER}", "{HILL}"],
            [$playerName, $this->hill->getName()],
            TextFormat::colorize($plugin->messages->getNested("broadcast.win", "broadcast.win"))
        ));
    }
}