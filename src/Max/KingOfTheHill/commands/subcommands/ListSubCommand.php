<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use Max\KingOfTheHill\Game;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class ListSubCommand extends BaseSubCommand {

    /** @var KingOfTheHill */
    protected Plugin $plugin;

    public function prepare(): void {
        $this->setPermission("kingofthehill.command.start");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $hills = $this->plugin->getHills();
        if (!$hills) {
            $sender->sendMessage(TextFormat::colorize($this->plugin->messages->getNested("fail.no-hills", "fail.no-hills")));
            return;
        }

        $currentHill = Game::getGame()?->getHill();
        $message = TextFormat::colorize($this->plugin->messages->getNested("list.title", "list.title"));
        foreach ($hills as $hill) {
            if ($hill === $currentHill) $status = TextFormat::colorize($this->plugin->messages->getNested("list.running", "list.running"));
            else if ($hill->getEnabled()) $status = TextFormat::colorize($this->plugin->messages->getNested("list.enabled", "list.enabled"));
            else $status = TextFormat::colorize($this->plugin->messages->getNested("list.disabled", "list.disabled"));
            $coords = (($pos1 = $hill->getCaptureZonePos1()) === null || ($pos2 = $hill->getCaptureZonePos2()) === null) ?
                TextFormat::colorize($this->plugin->messages->getNested("list.coords-not-set", "list.coords-not-set")) :
                str_replace(
                    ["{X}", "{Y}", "{Z}"],
                    [round($pos1->x + ($pos2->x - $pos1->x) / 2), round($pos1->y + ($pos2->y - $pos1->y) / 2), round($pos1->z + ($pos2->z - $pos1->z) / 2)],
                    TextFormat::colorize($this->plugin->messages->getNested("list.coords-set", "list.coords-set"))
                );
            $message .= "\n" . str_replace(
                ["{HILL}", "{STATUS}", "{COORDS}"],
                [$hill->getName(), $status, $coords],
                TextFormat::colorize($this->plugin->messages->getNested("list.line", "list.line"))
            );
        }
        $sender->sendMessage($message);
    }
}