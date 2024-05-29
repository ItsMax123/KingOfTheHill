<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands\subcommands\setup;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use Max\KingOfTheHill\Hill;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class SetTimeSubCommand extends BaseSubCommand {

    /** @var KingOfTheHill */
    protected Plugin $plugin;

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void {
        $this->setPermission("kingofthehill.command.setup");
        $this->registerArgument(0, new RawStringArgument("Hill", false));
        $this->registerArgument(1, new IntegerArgument("Time", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
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

        $time = $args["Time"];
        if ($time < 0) {
            $sender->sendMessage($this->plugin->getMessage("fail.time-not-positive"));
            return;
        }

        $hill->setTime($time * 20);
        $sender->sendMessage(str_replace(
            "{HILL}",
            $hill->getName(),
            $this->plugin->getMessage("success.set-time")
        ));
    }
}