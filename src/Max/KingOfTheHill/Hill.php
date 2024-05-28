<?php

declare(strict_types=1);

namespace Max\KingOfTheHill;

use JsonException;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\World;

class Hill {
    private KingOfTheHill $plugin;
    private string $name;
    private array $data;

    private bool $enabled;
    private int $time;
    private array $rewards;
    private ?Position $spawn;
    private ?World $captureZoneWorld;
    private ?Vector3 $captureZonePos1;
    private ?Vector3 $captureZonePos2;

    public function __construct(string $name, bool $enabled, int $time, array $rewards, ?Position $spawn, ?World $captureZoneWorld, ?Vector3 $captureZonePos1, ?Vector3 $captureZonePos2) {
        $this->plugin = KingOfTheHill::getInstance();
        $this->name = $name;

        $this->enabled = $enabled;
        $this->time = $time;
        $this->rewards = $rewards;
        $this->spawn = $spawn;
        $this->captureZoneWorld = $captureZoneWorld;
        $this->captureZonePos1 = $captureZonePos1;
        $this->captureZonePos2 = $captureZonePos2;

        $this->data = [
            "enabled" => $this->enabled,
            "time" => $this->time,
            "rewards" => $rewards,
            "spawn" => ($spawn === null) ? null : [
                "world" => $spawn->getWorld()->getFolderName(),
                "x" => $spawn->x,
                "y" => $spawn->y,
                "z" => $spawn->z,
            ],
            "capture-zone" => [
                "world" => ($captureZoneWorld === null) ? null : $captureZoneWorld->getFolderName(),
                "pos1" => ($captureZonePos1 === null) ? null : [
                    "x" => $captureZonePos1->x,
                    "y" => $captureZonePos1->y,
                    "z" => $captureZonePos1->z,
                ],
                "pos2" => ($captureZonePos2 === null) ? null : [
                    "x" => $captureZonePos2->x,
                    "y" => $captureZonePos2->y,
                    "z" => $captureZonePos2->z,
                ],
            ]
        ];
        $this->save();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getEnabled(): bool {
        return $this->enabled;
    }

    public function getTime(): int {
        return $this->time;
    }

    public function getRewards(): array {
        return $this->rewards;
    }

    public function hasReward(string $reward): bool {
        return in_array($reward, $this->rewards);
    }

    public function getSpawn(): ?Position {
        return $this->spawn;
    }

    public function getCaptureZoneWorld(): ?World {
        return $this->captureZoneWorld;
    }

    public function getCaptureZonePos1(): ?Vector3 {
        return $this->captureZonePos1;
    }

    public function getCaptureZonePos2(): ?Vector3 {
        return $this->captureZonePos2;
    }

    public function setName(string $name): void {
        $this->plugin->removeHill($this->name);
        $this->name = $name;
        $this->plugin->addHill($this);
    }

    public function setEnabled(bool $enabled): void {
        $this->enabled = $enabled;
        $this->data["enabled"] = $enabled;
        $this->save();
    }

    public function setTime(int $time): void {
        $this->time = $time;
        $this->data["time"] = $time;
        $this->save();
    }

    public function setRewards(array $rewards): void {
        $this->rewards = $rewards;
        $this->data["rewards"] = $rewards;
        $this->save();
    }

    public function addReward(string $reward): void {
        $this->rewards[] = $reward;
        $this->data["rewards"] = $this->rewards;
        $this->save();
    }

    public function removeReward(string $reward): void {
        unset($this->rewards[array_search($reward, $this->rewards)]);
        $this->data["rewards"] = $this->rewards;
        $this->save();
    }

    public function setSpawn(?Position $spawn): void {
        $this->spawn = $spawn;
        $this->data["spawn"] = ($spawn === null) ? null : [
            "world" => $spawn->getWorld()->getFolderName(),
            "x" => $spawn->x,
            "y" => $spawn->y,
            "z" => $spawn->z,
        ];
        $this->save();
    }

    public function setCaptureZonePos1(Position $position): void {
        $this->captureZoneWorld = $position->getWorld();
        $this->data["capture-zone"]["world"] = $this->captureZoneWorld->getFolderName();
        if ($this->captureZonePos2 === null) {
            $this->captureZonePos1 = $position->asVector3();
            $this->data["capture-zone"]["pos1"] = [
                "x" => $this->captureZonePos1->x,
                "y" => $this->captureZonePos1->y,
                "z" => $this->captureZonePos1->z,
            ];
            $this->save();
        } else {
            if ($this->captureZonePos1 === null) {
                $this->captureZonePos2 = new Vector3(
                    min($position->x, $this->captureZonePos2->x),
                    min($position->y, $this->captureZonePos2->y),
                    min($position->z, $this->captureZonePos2->z)
                );
            } else {
                $this->captureZonePos1->x = min($position->x, $this->captureZonePos2->x);
                $this->captureZonePos1->y = min($position->y, $this->captureZonePos2->y);
                $this->captureZonePos1->z = min($position->z, $this->captureZonePos2->z);
            }
            $this->captureZonePos2->x = max($position->x, $this->captureZonePos2->x);
            $this->captureZonePos2->y = max($position->y, $this->captureZonePos2->y);
            $this->captureZonePos2->z = max($position->z, $this->captureZonePos2->z);

            $this->setCaptureZones();
        }
    }

    public function setCaptureZonePos2(Position $position): void {
        $this->captureZoneWorld = $position->getWorld();
        $this->data["capture-zone"]["world"] = $this->captureZoneWorld->getFolderName();
        if ($this->captureZonePos1 === null) {
            $this->captureZonePos2 = $position->asVector3();
            $this->data["capture-zone"]["pos2"] = [
                "x" => $this->captureZonePos2->x,
                "y" => $this->captureZonePos2->y,
                "z" => $this->captureZonePos2->z,
            ];
            $this->save();
        } else {
            if ($this->captureZonePos2 === null) {
                $this->captureZonePos2 = new Vector3(
                    max($this->captureZonePos1->x, $position->x),
                    max($this->captureZonePos1->y, $position->y),
                    max($this->captureZonePos1->z, $position->z)
                );
            } else {
                $this->captureZonePos2->x = max($this->captureZonePos1->x, $position->x);
                $this->captureZonePos2->y = max($this->captureZonePos1->y, $position->y);
                $this->captureZonePos2->z = max($this->captureZonePos1->z, $position->z);
            }
            $this->captureZonePos1->x = min($this->captureZonePos1->x, $position->x);
            $this->captureZonePos1->y = min($this->captureZonePos1->y, $position->y);
            $this->captureZonePos1->z = min($this->captureZonePos1->z, $position->z);
            $this->setCaptureZones();
        }
    }

    private function setCaptureZones(): void {
        $this->data["capture-zone"]["pos1"] = [
            "x" => $this->captureZonePos1->x,
            "y" => $this->captureZonePos1->y,
            "z" => $this->captureZonePos1->z,
        ];
        $this->data["capture-zone"]["pos2"] = [
            "x" => $this->captureZonePos2->x,
            "y" => $this->captureZonePos2->y,
            "z" => $this->captureZonePos2->z,
        ];
        $this->save();
    }

    public function save(): void {
        $this->plugin->data->setNested("hills." . $this->name, $this->data);
        try {
            $this->plugin->data->save();
        } catch (JsonException) {
            $this->plugin->getLogger()->error("Failed to save KingOfTheHill data.");
        }
    }

    public function isInside(Position $position): bool {
        if ($this->captureZoneWorld === null || $this->captureZonePos1 === null || $this->captureZonePos2 === null) return false;
        if ($position->getWorld() === $this->captureZoneWorld &&
            $position->x <= $this->captureZonePos2->x &&
            $position->x >= $this->captureZonePos1->x &&
            $position->y <= $this->captureZonePos2->y &&
            $position->y >= $this->captureZonePos1->y &&
            $position->z <= $this->captureZonePos2->z &&
            $position->z >= $this->captureZonePos1->z
        ) return true;
        return false;
    }
}