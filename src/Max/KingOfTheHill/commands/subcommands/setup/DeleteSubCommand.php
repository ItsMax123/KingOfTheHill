<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands\subcommands\setup;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use Max\KingOfTheHill\Game;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class DeleteSubCommand extends BaseSubCommand {

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
        if (!$this->plugin->hasHill($name)) {
            $sender->sendMessage(str_replace(
                "{HILL}",
                $name,
                TextFormat::colorize($this->plugin->messages->getNested("fail.hill-doesnt-exist", "fail.hill-doesnt-exist"))
            ));
            return;
        }
        $hill = $this->plugin->getHill($name);
        if (($game = Game::getGame()) !== null && $game->getHill() === $hill) {
            $sender->sendMessage(str_replace(
                "{HILL}",
                $hill->getName(),
                TextFormat::colorize($this->plugin->messages->getNested("fail.hill-running", "fail.hill-running"))
            ));
            return;
        }
        $this->plugin->removeHill($name);
        $sender->sendMessage(str_replace(
            "{HILL}",
            $hill->getName(),
            TextFormat::colorize($this->plugin->messages->getNested("success.hill-delete", "success.hill-delete"))
        ));
    }
}