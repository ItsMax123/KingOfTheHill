<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands\subcommands\setup;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use Max\KingOfTheHill\Hill;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class CreateSubCommand extends BaseSubCommand {

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
        $name = $args["Hill"];
        $hill = Hill::getHill($name);
        if ($hill !== null) {
            $sender->sendMessage(str_replace(
                "{HILL}",
                $hill->getName(),
                $this->plugin->getMessage("fail.hill-already-exist")
            ));
            return;
        }

        new Hill($name, true, 6000, [], null, null, null, null);
        $sender->sendMessage(str_replace(
            "{HILL}",
            $name,
            $this->plugin->getMessage("success.hill-create")
        ));
    }
}