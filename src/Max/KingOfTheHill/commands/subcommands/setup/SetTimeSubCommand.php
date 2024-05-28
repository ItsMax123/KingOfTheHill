<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands\subcommands\setup;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

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
        if (!$this->plugin->hasHill($name)) {
            $sender->sendMessage(str_replace(
                "{HILL}",
                $name,
                TextFormat::colorize($this->plugin->messages->getNested("fail.hill-doesnt-exist", "fail.hill-doesnt-exist"))
            ));
            return;
        }

        $time = $args["Time"];
        if ($time < 0) {
            $sender->sendMessage(TextFormat::colorize($this->plugin->messages->getNested("fail.time-not-positive", "fail.time-not-positive")));
            return;
        }

        $hill = $this->plugin->getHill($name);
        $hill->setTime($time * 20);
        $sender->sendMessage(str_replace(
            "{HILL}",
            $hill->getName(),
            TextFormat::colorize($this->plugin->messages->getNested("success.set-time", "success.set-time"))
        ));
    }
}