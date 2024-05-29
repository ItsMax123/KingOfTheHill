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
            $this->plugin->getMessage("bossbar.title")
        ));
        $this->bar->setSubtitle(str_replace(
            ["{PLAYER}", "{TIME}"],
            ["N/A", gmdate("i:s", (int)($event->getHill()->getTime() / 20))],
            $this->plugin->getMessage("bossbar.subtitle")
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
            $this->plugin->getMessage("bossbar.subtitle")
        ));
    }

    public function onCaptureStop(CaptureStopEvent $event): void {
        $this->bar->setPercentage(0);
        $this->bar->setSubtitle(str_replace(
            ["{PLAYER}", "{TIME}"],
            ["N/A", gmdate("i:s", (int)($event->getHill()->getTime() / 20))],
            $this->plugin->getMessage("bossbar.subtitle")
        ));
    }

    public function onCaptureUpdate(CaptureUpdateEvent $event): void {
        $this->bar->setPercentage(($event->getHill()->getTime() - $event->getKing()->getCaptureTicksLeft()) / $event->getHill()->getTime());
        $this->bar->setSubtitle(str_replace(
            ["{PLAYER}", "{TIME}"],
            [$event->getKing()->getPlayer()->getName(), gmdate("i:s", (int)($event->getKing()->getCaptureTicksLeft() / 20))],
            $this->plugin->getMessage("bossbar.subtitle")
        ));
    }
}