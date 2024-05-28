<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use Max\KingOfTheHill\Game;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class SpawnSubCommand extends BaseSubCommand {

    /** @var KingOfTheHill */
    protected Plugin $plugin;

    public function prepare(): void {
        $this->setPermission("kingofthehill.command.start");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::colorize($this->plugin->messages->getNested("fail.in-game-command", "fail.in-game-command")));
            return;
        }

        $game = Game::getGame();
        if (is_null(Game::getGame())) {
            $sender->sendMessage(TextFormat::colorize($this->plugin->messages->getNested("fail.no-game", "fail.no-game")));
            return;
        }

        $hill = $game->getHill();
        $spawn = $hill->getSpawn();
        if (is_null($spawn)) {
            $sender->sendMessage(str_replace(
                "{HILL}",
                $hill->getName(),
                TextFormat::colorize($this->plugin->messages->getNested("fail.no-spawn", "fail.no-spawn"))
            ));
            return;
        }

        $sender->teleport($spawn);
        $sender->sendMessage(str_replace(
            "{HILL}",
            $hill->getName(),
            TextFormat::colorize($this->plugin->messages->getNested("success.teleport-spawn", "success.teleport-spawn"))
        ));
    }
}