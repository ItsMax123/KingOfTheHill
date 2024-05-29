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

class InfoSubCommand extends BaseSubCommand {

    /** @var KingOfTheHill */
    protected Plugin $plugin;

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void {
        $this->setPermission("kingofthehill.command.start");
        $this->registerArgument(0, new RawStringArgument("Hill", false));
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

        $message = $this->plugin->getMessage("info.title");
        $message .= "\n" . (str_replace(
            "{HILL}",
            $hill->getName(),
            $this->plugin->getMessage("info.name")
        ));
        if ($hill === $this->plugin->getRunningHill()) $status = $this->plugin->getMessage("info.running");
        else if ($hill->getEnabled()) $status = $this->plugin->getMessage("info.enabled");
        else $status = $this->plugin->getMessage("info.disabled");
        $message .= "\n" . (str_replace(
            "{STATUS}",
            $status,
            $this->plugin->getMessage("info.status")
        ));
        $message .= "\n" . (str_replace(
            "{TIME}",
            gmdate("i:s", (int)floor($hill->getTime() / 20)),
            $this->plugin->getMessage("info.time")
        ));
        $message .= "\n" . (str_replace(
                "{COORDS}",
                ($spawn = $hill->getSpawn()) === null ?
                $this->plugin->getMessage("info.coords-not-set") :
                str_replace(
                    ["{X}", "{Y}", "{Z}"],
                    [round($spawn->x), round($spawn->y), round($spawn->z)],
                    $this->plugin->getMessage("info.coords-set")
                ),
                $this->plugin->getMessage("info.spawn")
            ));
        $message .= "\n" . (str_replace(
                "{COORDS}",
                ($pos1 = $hill->getCaptureZonePos1()) === null || ($pos2 = $hill->getCaptureZonePos2()) === null ?
                $this->plugin->getMessage("info.coords-not-set") :
                str_replace(
                    ["{X}", "{Y}", "{Z}"],
                    [round($pos1->x + ($pos2->x - $pos1->x) / 2), round($pos1->y + ($pos2->y - $pos1->y) / 2), round($pos1->z + ($pos2->z - $pos1->z) / 2)],
                    $this->plugin->getMessage("info.coords-set")
                ),
                $this->plugin->getMessage("info.location")
            ));
        $message .= "\n" . $this->plugin->getMessage("info.rewards-title");
        foreach ($hill->getRewards() as $reward) {
            $message .= "\n" . (str_replace(
                "{REWARD}",
                $reward,
                    $this->plugin->getMessage("info.rewards-line")
            ));
        }
        $sender->sendMessage($message);
    }
}