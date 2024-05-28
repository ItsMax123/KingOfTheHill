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

class StartSubCommand extends BaseSubCommand {

    /** @var KingOfTheHill */
    protected Plugin $plugin;

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void {
        $this->setPermission("kingofthehill.command.start");
        $this->registerArgument(0, new RawStringArgument("Hill", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (isset($args["Hill"])) {
            $hill = $this->plugin->getHill($args["Hill"]);
            if ($hill === null) {
                $sender->sendMessage(str_replace(
                    "{HILL}",
                    $args["Hill"],
                    TextFormat::colorize($this->plugin->messages->getNested("fail.hill-doesnt-exist", "fail.hill-doesnt-exist"))
                ));
                return;
            }
        } else {
            $hills = array_filter($this->plugin->getHills(), function($hill) {
                return $hill->getEnabled();
            });
            if (!$hills) {
                $sender->sendMessage(TextFormat::colorize($this->plugin->messages->getNested("fail.no-hills", "fail.no-hills")));
                return;
            }
            $hill = $hills[array_rand($hills)];
        }
        if (!Game::startGame($hill)) {
            $sender->sendMessage(TextFormat::colorize($this->plugin->messages->getNested("fail.game-cant-start", "fail.game-cant-start")));
        }
    }
}