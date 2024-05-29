<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class StopSubCommand extends BaseSubCommand {

    /** @var KingOfTheHill */
    protected Plugin $plugin;

    public function prepare(): void {
        $this->setPermission("kingofthehill.command.stop");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $this->plugin->stopGame();
    }
}