<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use Max\KingOfTheHill\Hill;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

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
            $hill = Hill::getHill($args["Hill"]);
            if ($hill === null) {
                $sender->sendMessage(str_replace(
                    "{HILL}",
                    $args["Hill"],
                    $this->plugin->getMessage("fail.hill-doesnt-exist")
                ));
                return;
            }
        } else {
            $hills = array_filter(Hill::getHills(), function($hill) {
                return $hill->getEnabled();
            });
            if (!$hills) {
                $sender->sendMessage($this->plugin->getMessage("fail.no-hills"));
                return;
            }
            $hill = $hills[array_rand($hills)];
        }
        if (!$this->plugin->startGame($hill)) {
            $sender->sendMessage($this->plugin->getMessage("fail.game-cant-start"));
        }
    }
}