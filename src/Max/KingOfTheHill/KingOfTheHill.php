<?php

declare(strict_types=1);

namespace Max\KingOfTheHill;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use Max\KingOfTheHill\addons\bossbar\BossBarListener;
use Max\KingOfTheHill\addons\scorehud\ScoreHudListener;
use Max\KingOfTheHill\commands\KothCommand;
use Max\KingOfTheHill\events\game\GameStartEvent;
use Max\KingOfTheHill\events\game\GameStopEvent;
use Max\KingOfTheHill\tasks\AutoStartTask;
use Max\KingOfTheHill\tasks\GameTask;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class KingOfTheHill extends PluginBase {
    private static KingOfTheHill $instance;

    private Config $config;
    private Config $messages;

    public Config $data;

    private ?GameTask $task = null;

    public function onLoad(): void {
        self::$instance = $this;
    }

    /**
     * @throws HookAlreadyRegistered
     */
    public function onEnable(): void {
        if(!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->saveDefaultConfig();
        $this->config = $this->getConfig();

        $this->saveResource("messages.yml");
        $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);

        $this->saveResource("data.yml");
        $this->data = new Config($this->getDataFolder() . "data.yml", Config::YAML);

        //try {
            foreach ($this->data->get("hills") as $name => $data) {
                new Hill(
                    $name,
                    $data["enabled"],
                    $data["time"],
                    $data["rewards"],
                    $data["spawn"] === null ? null : new Position($data["spawn"]["x"], $data["spawn"]["y"], $data["spawn"]["z"], $this->getServer()->getWorldManager()->getWorldByName($data["spawn"]["world"])),
                    $data["capture-zone"]["world"] === null ? null : $this->getServer()->getWorldManager()->getWorldByName($data["capture-zone"]["world"]),
                    $data["capture-zone"]["pos1"] === null ? null : new Vector3($data["capture-zone"]["pos1"]["x"], $data["capture-zone"]["pos1"]["y"], $data["capture-zone"]["pos1"]["z"]),
                    $data["capture-zone"]["pos2"] === null ? null : new Vector3($data["capture-zone"]["pos2"]["x"], $data["capture-zone"]["pos2"]["y"], $data["capture-zone"]["pos2"]["z"])
                );
            }
        //} catch () {
        //    $this->getLogger()->error("Failed to load hill " . $name . ", invalid data.");
        //}

        if ($this->config->get("scorehud") && $this->getServer()->getPluginManager()->getPlugin("ScoreHud") !== null) {
            $this->getServer()->getPluginManager()->registerEvents(new ScoreHudListener(), $this);
        }

        if ($this->config->get("bossbar", true)) {
            $this->getServer()->getPluginManager()->registerEvents(new BossBarListener($this), $this);
        }

        if ($this->config->getNested("autostart.enabled", false) && ($times = $this->config->getNested("autostart.times", []))) {
            date_default_timezone_set($this->config->getNested("autostart.timezone", "UTC"));
            $this->getScheduler()->scheduleRepeatingTask(new AutoStartTask($this, $times, $this->config->getNested("autostart.min-players", 0)), $this->config->getNested("autostart.check-interval", 1200));
        }

        $this->getServer()->getCommandMap()->register("kingofthehill", new KothCommand($this, "koth", "The King of the Hill Command"));
    }

    public static function getInstance(): KingOfTheHill {
        return self::$instance;
    }

    public function getMessage(string $message): string {
        return TextFormat::colorize($this->messages->getNested($message, $message));
    }

    public function startGame(Hill $hill): bool {
        if (!is_null($this->task)) return false;
        $gameStartEvent = new GameStartEvent($hill);
        $gameStartEvent->call();
        if ($gameStartEvent->isCancelled()) return false;
        $this->task = new GameTask($gameStartEvent->getHill());
        $this->getScheduler()->scheduleRepeatingTask($this->task, $this->config->get("update-interval", 20));
        $this->getServer()->broadcastMessage(str_replace(
            ["{HILL}"],
            [$hill->getName()],
            $this->getMessage("broadcast.game-started")
        ));
        return true;
    }

    public function stopGame(): void {
        if (is_null($this->task)) return;
        $gameStopEvent = new GameStopEvent($this->task->hill);
        $gameStopEvent->call();
        $this->getServer()->broadcastMessage(str_replace(
            ["{HILL}"],
            [$this->task->hill->getName()],
            $this->getMessage("broadcast.game-stopped")
        ));
        $this->task->getHandler()->cancel();
        $this->task = null;
    }

    public function isRunning(): bool {
        return !is_null($this->task);
    }

    public function getRunningHill(): ?Hill {
        return $this->task?->hill;
    }

    public function getRunningKing(): ?King {
        return $this->task?->king;
    }
}