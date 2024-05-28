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
use pocketmine\utils\TextFormat;

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
        if ($this->plugin->hasHill($name)) {
            $sender->sendMessage(str_replace(
                "{HILL}",
                $name,
                TextFormat::colorize($this->plugin->messages->getNested("fail.hill-already-exist", "fail.hill-already-exist"))
            ));
            return;
        }

        $this->plugin->addHill(new Hill($name, true, 6000, [], null, null, null, null));
        $sender->sendMessage(str_replace(
            "{HILL}",
            $name,
            TextFormat::colorize($this->plugin->messages->getNested("success.hill-create", "success.hill-create"))
        ));
    }
}