<?php

declare(strict_types=1);

namespace Max\KingOfTheHill;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use ErrorException;
use JsonException;
use Max\KingOfTheHill\addons\bossbar\BossBarListener;
use Max\KingOfTheHill\addons\scorehud\ScoreHudListener;
use Max\KingOfTheHill\commands\KothCommand;
use Max\KingOfTheHill\tasks\AutoStartTask;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\world\Position;

class KingOfTheHill extends PluginBase {
    private static KingOfTheHill $instance;

    /** @var Hill[] */
    private array $hills = [];

    public Config $config;
    public Config $data;
    public Config $messages;

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
        $this->saveResource("data.yml");
        $this->saveResource("messages.yml");
        $this->config = $this->getConfig();
        $this->data = new Config($this->getDataFolder() . "data.yml", Config::YAML);
        $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        if (is_array($this->data->get("hills"))) {
            foreach ($this->data->get("hills") as $name => $data) {
                try {
                    $this->addHill(new Hill(
                        $name,
                        $data["enabled"],
                        $data["time"],
                        $data["rewards"],
                        $data["spawn"] === null ? null : new Position($data["spawn"]["x"], $data["spawn"]["y"], $data["spawn"]["z"], $this->getServer()->getWorldManager()->getWorldByName($data["spawn"]["world"])),
                        $data["capture-zone"]["world"] === null ? null : $this->getServer()->getWorldManager()->getWorldByName($data["capture-zone"]["world"]),
                        $data["capture-zone"]["pos1"] === null ? null : new Vector3($data["capture-zone"]["pos1"]["x"], $data["capture-zone"]["pos1"]["y"], $data["capture-zone"]["pos1"]["z"]),
                        $data["capture-zone"]["pos2"] === null ? null : new Vector3($data["capture-zone"]["pos2"]["x"], $data["capture-zone"]["pos2"]["y"], $data["capture-zone"]["pos2"]["z"])
                    ));
                } catch (ErrorException) {
                    $this->getLogger()->error("Failed to load hill " . $name . ", invalid data.");
                }
            }
        }
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

    public function getHills(): array {
        return $this->hills;
    }

    public function getHill(string $name): ?Hill {
        return $this->hills[strtolower($name)] ?? null;
    }

    public function hasHill(string $name): bool {
        return array_key_exists(strtolower($name), $this->hills);
    }

    public function addHill(Hill $hill): void {
        $this->hills[strtolower($hill->getName())] = $hill;
    }

    public function removeHill(string $name): void {
        unset($this->hills[strtolower($name)]);
        $this->data->removeNested("hills." . $name);
        try {
            $this->data->save();
        } catch (JsonException) {
            $this->getLogger()->error("Failed to remove Hill data.");
        }
    }
}