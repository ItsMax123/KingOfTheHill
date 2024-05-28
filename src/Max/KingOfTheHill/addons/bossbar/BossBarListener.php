<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\addons\bossbar;

use Max\KingOfTheHill\events\capture\CaptureStartEvent;
use Max\KingOfTheHill\events\capture\CaptureStopEvent;
use Max\KingOfTheHill\events\capture\CaptureUpdateEvent;
use Max\KingOfTheHill\events\game\GameStartEvent;
use Max\KingOfTheHill\events\game\GameStopEvent;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\TextFormat;
use xenialdan\apibossbar\BossBar;

final class BossBarListener implements Listener {
    private KingOfTheHill $plugin;
    public BossBar $bar;
    public bool $show = false;

    public function __construct(KingOfTheHill $plugin) {
        $this->plugin = $plugin;
        $this->bar = (new BossBar())->setColor(2);
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $this->bar->addPlayer($player);
        if (!$this->show) $this->bar->hideFrom([$player]);
    }

    public function onPlayerLeave(PlayerQuitEvent $event): void {
        $this->bar->removePlayer($event->getPlayer());
    }

    public function onGameStart(GameStartEvent $event): void {
        $this->bar->setPercentage(0);
        $this->bar->setTitle(str_replace(
            "{HILL}",
            $event->getHill()->getName(),
            TextFormat::colorize($this->plugin->messages->getNested("bossbar.title", "bossbar.title"))
        ));
        $this->bar->setSubtitle(str_replace(
            ["{PLAYER}", "{TIME}"],
            ["N/A", gmdate("i:s", (int)($event->getHill()->getTime() / 20))],
            TextFormat::colorize($this->plugin->messages->getNested("bossbar.subtitle", "bossbar.subtitle"))
        ));
        $this->bar->showToAll();
        $this->show = true;
    }

    public function onGameStop(GameStopEvent $event): void {
        $this->bar->hideFromAll();
        $this->show = false;
    }

    public function onCaptureStart(CaptureStartEvent $event): void {
        $this->bar->setSubtitle(str_replace(
            ["{PLAYER}", "{TIME}"],
            [$event->getPlayer()->getName(), gmdate("i:s", (int)($event->getTime() / 20))],
            TextFormat::colorize($this->plugin->messages->getNested("bossbar.subtitle", "bossbar.subtitle"))
        ));
    }

    public function onCaptureStop(CaptureStopEvent $event): void {
        $this->bar->setPercentage(0);
        $this->bar->setSubtitle(str_replace(
            ["{PLAYER}", "{TIME}"],
            ["N/A", gmdate("i:s", (int)($event->getGame()->getHill()->getTime() / 20))],
            TextFormat::colorize($this->plugin->messages->getNested("bossbar.subtitle", "bossbar.subtitle"))
        ));
    }

    public function onCaptureUpdate(CaptureUpdateEvent $event): void {
        $this->bar->setPercentage(($event->getGame()->getHill()->getTime() - $event->getGame()->getKing()->getCaptureTicksLeft()) / $event->getGame()->getHill()->getTime());
        $this->bar->setSubtitle(str_replace(
            ["{PLAYER}", "{TIME}"],
            [$event->getGame()->getKing()->getPlayer()->getName(), gmdate("i:s", (int)($event->getGame()->getKing()->getCaptureTicksLeft() / 20))],
            TextFormat::colorize($this->plugin->messages->getNested("bossbar.subtitle", "bossbar.subtitle"))
        ));
    }
}