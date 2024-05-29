<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands\subcommands\setup;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use Max\KingOfTheHill\Hill;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

class SetPos1SubCommand extends BaseSubCommand {

    /** @var KingOfTheHill */
    protected Plugin $plugin;

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void {
        $this->setPermission("kingofthehill.command.setup");
        $this->registerArgument(0, new RawStringArgument("Hill", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage($this->plugin->getMessage("fail.in-game-command"));
            return;
        }

        $name = $args["Hill"];
        $hill = Hill::getHill($name);
        if ($hill === null) {
            $sender->sendMessage(str_replace(
                "{HILL}",
                $name,
                $this->plugin->getMessage("fail.hill-doesnt-exist")
            ));
            return;
        }

        $hill->setCaptureZonePos1($sender->getPosition());
        $sender->sendMessage(str_replace(
            "{HILL}",
            $hill->getName(),
            $this->plugin->getMessage("success.set-pos1")
        ));
    }
}