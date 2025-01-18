<?php

namespace Max\KingOfTheHill\addons\discord;

use Max\KingOfTheHill\addons\discord\tasks\SendDiscordWebhookAsyncTask;
use Max\KingOfTheHill\events\capture\CaptureWinEvent;
use Max\KingOfTheHill\events\game\GameStartEvent;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\utils\Config;

class DiscordWebhookListener implements Listener {
    private string $url;
    private array $start;
    private array $win;

    public function __construct(string $url, Config $config) {
        $this->url = $url;
        $this->start = $config->get("start", []);
        $this->win = $config->get("win", []);
    }

    public function onGameStart(GameStartEvent $event): void {
        $hill = $event->getHill();
        $pos1 = $hill->getCaptureZonePos1();
        $pos2 = $hill->getCaptureZonePos2();
        Server::getInstance()->getAsyncPool()->submitTask(new SendDiscordWebhookAsyncTask($this->url, str_replace(
            ["{HILL}", "{REWARDS}", "{X}", "{Y}", "{Z}"],
            [$hill->getName(), implode(", ", $hill->getRewards()), round($pos1->x + ($pos2->x - $pos1->x) / 2), round($pos1->y + ($pos2->y - $pos1->y) / 2), round($pos1->z + ($pos2->z - $pos1->z) / 2)],
            json_encode($this->start)
        )));
    }

    public function onCapture(CaptureWinEvent $event): void {
        $hill = $event->getHill();
        $pos1 = $hill->getCaptureZonePos1();
        $pos2 = $hill->getCaptureZonePos2();
        Server::getInstance()->getAsyncPool()->submitTask(new SendDiscordWebhookAsyncTask($this->url, str_replace(
            ["{HILL}", "{WINNER}", "{REWARDS}", "{X}", "{Y}", "{Z}"],
            [$hill->getName(), $event->getPlayer()->getName(), implode(", ", $hill->getRewards()), round($pos1->x + ($pos2->x - $pos1->x) / 2), round($pos1->y + ($pos2->y - $pos1->y) / 2), round($pos1->z + ($pos2->z - $pos1->z) / 2)],
            json_encode($this->win)
        )));
    }
}