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
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\event\Listener;

final class ScoreHudListener implements Listener {
    public function onTagResolve(TagsResolveEvent $event): void {
        $tag = $event->getTag();
        switch ($tag->getName()) {
            case "kingofthehill.hill":
                $tag->setValue(($hill = KingOfTheHill::getInstance()->getRunningHill()) === null ? "" : $hill->getName());
                break;
            case "kingofthehill.king":
                $tag->setValue(($king = KingOfTheHill::getInstance()->getRunningKing()) === null ? "" : $king->getPlayer()->getName());
                break;
            case "kingofthehill.time":
                $tag->setValue(($hill = KingOfTheHill::getInstance()->getRunningHill()) === null ? "" : gmdate("i:s", (int)($hill->getTime() / 20)));
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
        (new ServerTagUpdateEvent(new ScoreTag("kingofthehill.time", gmdate("i:s", (int)($event->getHill()->getTime() / 20)))))->call();
    }

    public function onCaptureUpdate(CaptureUpdateEvent $event): void {
        (new ServerTagUpdateEvent(new ScoreTag("kingofthehill.time", gmdate("i:s", (int)($event->getKing()->getCaptureTicksLeft() / 20)))))->call();
    }
}