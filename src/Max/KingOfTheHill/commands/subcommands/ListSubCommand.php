<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use Max\KingOfTheHill\Hill;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class ListSubCommand extends BaseSubCommand {

    /** @var KingOfTheHill */
    protected Plugin $plugin;

    public function prepare(): void {
        $this->setPermission("kingofthehill.command.start");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $hills = Hill::getHills();
        if (!$hills) {
            $sender->sendMessage($this->plugin->getMessage("fail.no-hills"));
            return;
        }

        $runningHill = $this->plugin->getRunningHill();
        $message = $this->plugin->getMessage("list.title");
        foreach ($hills as $hill) {
            if ($hill === $runningHill) $status = $this->plugin->getMessage("list.running");
            else if ($hill->getEnabled()) $status = $this->plugin->getMessage("list.enabled");
            else $status = $this->plugin->getMessage("list.disabled");
            $coords = (($pos1 = $hill->getCaptureZonePos1()) === null || ($pos2 = $hill->getCaptureZonePos2()) === null) ?
                $this->plugin->getMessage("list.coords-not-set") :
                str_replace(
                    ["{X}", "{Y}", "{Z}"],
                    [round($pos1->x + ($pos2->x - $pos1->x) / 2), round($pos1->y + ($pos2->y - $pos1->y) / 2), round($pos1->z + ($pos2->z - $pos1->z) / 2)],
                    $this->plugin->getMessage("list.coords-set")
                );
            $message .= "\n" . str_replace(
                ["{HILL}", "{STATUS}", "{COORDS}"],
                [$hill->getName(), $status, $coords],
                $this->plugin->getMessage("list.line")
            );
        }
        $sender->sendMessage($message);
    }
}