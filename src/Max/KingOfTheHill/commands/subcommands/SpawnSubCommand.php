<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

class SpawnSubCommand extends BaseSubCommand {

    /** @var KingOfTheHill */
    protected Plugin $plugin;

    public function prepare(): void {
        $this->setPermission("kingofthehill.command.start");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage($this->plugin->getMessage("fail.in-game-command"));
            return;
        }

        $runningHill = $this->plugin->getRunningHill();
        if ($runningHill === null) {
            $sender->sendMessage($this->plugin->getMessage("fail.no-game"));
            return;
        }

        $spawn = $runningHill->getSpawn();
        if (is_null($spawn)) {
            $sender->sendMessage(str_replace(
                "{HILL}",
                $runningHill->getName(),
                $this->plugin->getMessage("fail.no-spawn")
            ));
            return;
        }

        $sender->teleport($spawn);
        $sender->sendMessage(str_replace(
            "{HILL}",
            $runningHill->getName(),
            $this->plugin->getMessage("success.teleport-spawn")
        ));
    }
}