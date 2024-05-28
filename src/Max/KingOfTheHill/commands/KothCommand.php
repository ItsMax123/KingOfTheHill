<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\commands;

use CortexPE\Commando\BaseCommand;
use Max\KingOfTheHill\commands\subcommands\InfoSubCommand;
use Max\KingOfTheHill\commands\subcommands\ListSubCommand;
use Max\KingOfTheHill\commands\subcommands\setup\AddRewardSubCommand;
use Max\KingOfTheHill\commands\subcommands\setup\CreateSubCommand;
use Max\KingOfTheHill\commands\subcommands\setup\DeleteSubCommand;
use Max\KingOfTheHill\commands\subcommands\setup\DelRewardSubCommand;
use Max\KingOfTheHill\commands\subcommands\setup\DelSpawnSubCommand;
use Max\KingOfTheHill\commands\subcommands\setup\DisableSubCommand;
use Max\KingOfTheHill\commands\subcommands\setup\EnableSubCommand;
use Max\KingOfTheHill\commands\subcommands\setup\SetPos1SubCommand;
use Max\KingOfTheHill\commands\subcommands\setup\SetPos2SubCommand;
use Max\KingOfTheHill\commands\subcommands\setup\SetSpawnSubCommand;
use Max\KingOfTheHill\commands\subcommands\setup\SetTimeSubCommand;
use Max\KingOfTheHill\commands\subcommands\SpawnSubCommand;
use Max\KingOfTheHill\commands\subcommands\StartSubCommand;
use Max\KingOfTheHill\commands\subcommands\StopSubCommand;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class KothCommand extends BaseCommand {

    /** @var KingOfTheHill */
    protected Plugin $plugin;

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $this->sendUsage();
    }

    public function prepare(): void {
        $this->setPermissions([
            "kingofthehill.command.start",
            "kingofthehill.command.stop",
            "kingofthehill.command.info",
            "kingofthehill.command.list",
            "kingofthehill.command.spawn",
            "kingofthehill.command.setup",
        ]);

        $this->registerSubCommand(new ListSubCommand($this->plugin, "list", "Lists all Hills."));
        $this->registerSubCommand(new InfoSubCommand($this->plugin, "info", "Get information about a Hill."));
        $this->registerSubCommand(new SpawnSubCommand($this->plugin, "spawn", "Teleport to Hill's spawn point."));

        $this->registerSubCommand(new StartSubCommand($this->plugin, "start", "Start a Game"));
        $this->registerSubCommand(new StopSubCommand($this->plugin, "stop", "Stop the Game"));

        $this->registerSubCommand(new CreateSubCommand($this->plugin, "create", "Create a Hill"));
        $this->registerSubCommand(new DeleteSubCommand($this->plugin, "delete", "Delete a Hill"));

        $this->registerSubCommand(new EnableSubCommand($this->plugin, "enable", "Allow the Hill to be randomly selected during autostart or '/koth start'"));
        $this->registerSubCommand(new DisableSubCommand($this->plugin, "disable", "Prevent a Hill from being randomly selected during autostart or '/koth start'"));

        $this->registerSubCommand(new SetSpawnSubCommand($this->plugin, "setspawn", "Set the spawn point for a Hill"));
        $this->registerSubCommand(new DelSpawnSubCommand($this->plugin, "delspawn", "Delete the spawn point for a Hill"));

        $this->registerSubCommand(new SetPos1SubCommand($this->plugin, "setpos1", "Set the first corner for a Hill's Capture Zone"));
        $this->registerSubCommand(new SetPos2SubCommand($this->plugin, "setpos2", "Set the second corner for a Hill's Capture Zone"));

        $this->registerSubCommand(new AddRewardSubCommand($this->plugin, "addreward", "Add a reward to a Hill"));
        $this->registerSubCommand(new DelRewardSubCommand($this->plugin, "delreward", "Delete a reward from a Hill"));

        $this->registerSubCommand(new SetTimeSubCommand($this->plugin, "settime", "Set the time in seconds to capture for a Hill"));
    }
}