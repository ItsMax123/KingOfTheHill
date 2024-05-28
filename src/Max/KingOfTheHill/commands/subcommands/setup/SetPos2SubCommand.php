<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands\subcommands\setup;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class SetPos2SubCommand extends BaseSubCommand {

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
            $sender->sendMessage(TextFormat::colorize($this->plugin->messages->getNested("fail.in-game-command", "fail.in-game-command")));
            return;
        }

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
        $hill->setCaptureZonePos2($sender->getPosition());
        $sender->sendMessage(str_replace(
            "{HILL}",
            $hill->getName(),
            TextFormat::colorize($this->plugin->messages->getNested("success.set-pos2", "success.set-pos2"))
        ));
    }
}