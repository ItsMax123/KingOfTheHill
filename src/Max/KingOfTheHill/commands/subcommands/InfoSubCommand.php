<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use Max\KingOfTheHill\Game;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class InfoSubCommand extends BaseSubCommand {

    /** @var KingOfTheHill */
    protected Plugin $plugin;

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void {
        $this->setPermission("kingofthehill.command.start");
        $this->registerArgument(0, new RawStringArgument("Hill", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $name = $args["Hill"];
        if (!$this->plugin->hasHill($name)) {
            $sender->sendMessage(str_replace(
                "{HILL}",
                $name,
                TextFormat::colorize($this->plugin->messages->getNested("fail.hill-doesnt-exist", "fail.hill-doesnt-exist"))
            ));
            return;
        }

        $hill = $this->plugin->getHill($name);
        $message = TextFormat::colorize($this->plugin->messages->getNested("info.title", "info.title"));
        $message .= "\n" . (str_replace(
            "{HILL}",
            $hill->getName(),
            TextFormat::colorize($this->plugin->messages->getNested("info.name", "info.name"))
        ));
        if ($hill === Game::getGame()?->getHill()) $status = TextFormat::colorize($this->plugin->messages->getNested("info.running", "info.running"));
        else if ($hill->getEnabled()) $status = TextFormat::colorize($this->plugin->messages->getNested("info.enabled", "info.enabled"));
        else $status = TextFormat::colorize($this->plugin->messages->getNested("info.disabled", "info.disabled"));
        $message .= "\n" . (str_replace(
            "{STATUS}",
            $status,
            TextFormat::colorize($this->plugin->messages->getNested("info.status", "info.status"))
        ));
        $message .= "\n" . (str_replace(
            "{TIME}",
            gmdate("i:s", (int)floor($hill->getTime() / 20)),
            TextFormat::colorize($this->plugin->messages->getNested("info.time", "info.time"))
        ));
        $message .= "\n" . (str_replace(
                "{COORDS}",
                ($spawn = $hill->getSpawn()) === null ?
                TextFormat::colorize($this->plugin->messages->getNested("info.coords-not-set", "info.coords-not-set")) :
                str_replace(
                    ["{X}", "{Y}", "{Z}"],
                    [round($spawn->x), round($spawn->y), round($spawn->z)],
                    TextFormat::colorize($this->plugin->messages->getNested("info.coords-set", "info.coords-set"))
                ),
                TextFormat::colorize($this->plugin->messages->getNested("info.spawn", "info.spawn"))
            ));
        $message .= "\n" . (str_replace(
                "{COORDS}",
                ($pos1 = $hill->getCaptureZonePos1()) === null || ($pos2 = $hill->getCaptureZonePos2()) === null ?
                TextFormat::colorize($this->plugin->messages->getNested("info.coords-not-set", "info.coords-not-set")) :
                str_replace(
                    ["{X}", "{Y}", "{Z}"],
                    [round($pos1->x + ($pos2->x - $pos1->x) / 2), round($pos1->y + ($pos2->y - $pos1->y) / 2), round($pos1->z + ($pos2->z - $pos1->z) / 2)],
                    TextFormat::colorize($this->plugin->messages->getNested("info.coords-set", "info.coords-set"))
                ),
                TextFormat::colorize($this->plugin->messages->getNested("info.location", "info.location"))
            ));
        $message .= "\n" . TextFormat::colorize($this->plugin->messages->getNested("info.rewards-title", "info.rewards-title"));
        foreach ($hill->getRewards() as $reward) {
            $message .= "\n" . (str_replace(
                "{REWARD}",
                $reward,
                TextFormat::colorize($this->plugin->messages->getNested("info.rewards-line", "info.rewards-line"))
            ));
        }
        $sender->sendMessage($message);
    }
}