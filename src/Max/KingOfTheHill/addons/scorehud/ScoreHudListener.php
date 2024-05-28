<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\addons\scorehud;

use Ifera\ScoreHud\event\ServerTagUpdateEvent;
use Ifera\ScoreHud\event\TagsResolveEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Max\KingOfTheHill\events\capture\CaptureStartEvent;
use Max\KingOfTheHill\events\capture\CaptureStopEvent;
use Max\KingOfTheHill\events\capture\CaptureUpdateEvent;
use Max\KingOfTheHill\events\game\GameStartEvent;
use Max\KingOfTheHill\events\game\GameStopEvent;
use pocketmine\event\Listener;

final class ScoreHudListener implements Listener {
    public function onTagResolve(TagsResolveEvent $event): void {
        $tag = $event->getTag();
        switch ($tag->getName()) {
            case "kingofthehill.hill":
            case "kingofthehill.king":
            case "kingofthehill.time":
                $tag->setValue("");
                break;
        }
    }

    public function onGameStart(GameStartEvent $event): void {
        (new ServerTagUpdateEvent(new ScoreTag("kingofthehill.hill", $event->getHill()->getName())))->call();
        (new ServerTagUpdateEvent(new ScoreTag("kingofthehill.king", "N/A")))->call();
        (new ServerTagUpdateEvent(new ScoreTag("kingofthehill.time", gmdate("i:s", (int)($event->getHill()->getTime() / 20)))))->call();
    }

    public function onGameStop(GameStopEvent $event): void {
        (new ServerTagUpdateEvent(new ScoreTag("kingofthehill.hill", "")))->call();
        (new ServerTagUpdateEvent(new ScoreTag("kingofthehill.king", "")))->call();
        (new ServerTagUpdateEvent(new ScoreTag("kingofthehill.time", "")))->call();
    }

    public function onCaptureStart(CaptureStartEvent $event): void {
        (new ServerTagUpdateEvent(new ScoreTag("kingofthehill.king", $event->getPlayer()->getName())))->call();
        (new ServerTagUpdateEvent(new ScoreTag("kingofthehill.time", gmdate("i:s", (int)($event->getTime() / 20)))))->call();
    }

    public function onCaptureStop(CaptureStopEvent $event): void {
        (new ServerTagUpdateEvent(new ScoreTag("kingofthehill.king", "N/A")))->call();
        (new ServerTagUpdateEvent(new ScoreTag("kingofthehill.time", gmdate("i:s", (int)($event->getGame()->getHill()->getTime() / 20)))))->call();
    }

    public function onCaptureUpdate(CaptureUpdateEvent $event): void {
        (new ServerTagUpdateEvent(new ScoreTag("kingofthehill.time", gmdate("i:s", (int)($event->getGame()->getKing()->getCaptureTicksLeft() / 20)))))->call();
    }
}