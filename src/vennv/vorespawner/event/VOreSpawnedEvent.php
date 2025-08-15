<?php

declare(strict_types=1);

namespace vennv\vorespawner\event;

use pocketmine\event\Cancellable;
use pocketmine\event\Event;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class VOreSpawnedEvent extends Event implements Cancellable
{

    private bool $cancelled = false;
    private Player $player;
    private string $type;
    private int $level;
    private Vector3 $vector3;

    public function __construct(Player $player, string $type, int $level, Vector3 $vector3)
    {
        $this->player = $player;
        $this->type = $type;
        $this->level = $level;
        $this->vector3 = $vector3;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getVector3(): Vector3
    {
        return $this->vector3;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
    }
}

