<?php

declare(strict_types=1);

namespace Max\KingOfTheHill\tasks;

use Max\KingOfTheHill\Hill;
use Max\KingOfTheHill\KingOfTheHill;
use pocketmine\scheduler\Task;

class AutoStartTask extends Task {
    public KingOfTheHill $plugin;

    public float $lastCheck;

    /** @var float[] */
    public array $times;

    public int $minPlayers;

    /** @param float[] $times */
    public function __construct(KingOfTheHill $plugin, array $times, int $minPlayers) {
        $now = (float)date("G.i");
        $part1 = [];
        $part2 = [];
        foreach ($times as $time) {
            if ($time > $now && $time <= 24) {
                $part1[] = $time;
            } else if ($time >= 0 && $time <= $now) {
                $part2[] = $time;
            }
        }
        sort($part1);
        sort($part2);
        $this->plugin = $plugin;
        $this->lastCheck = $now;
        $this->times = array_merge($part1, $part2);
        $this->minPlayers = $minPlayers;
    }

    public function onRun(): void {
        $now = (float)date("G.i");
        if ($this->times[0] <= $this->lastCheck || $this->times[0] > $now) return;
        $this->lastCheck = $now;
        $this->times[] = array_shift($this->times);
        if (count($this->plugin->getServer()->getOnlinePlayers()) < $this->minPlayers) return;
        $hills = array_filter(Hill::getHills(), function($hill) {
            return $hill->getEnabled();
        });
        if (!$hills) {
            $this->plugin->getLogger()->info("[AutoStart] " . $this->plugin->getMessage("fail.no-hills"));
            return;
        }
        $this->plugin->startGame($hills[array_rand($hills)]);
    }
}