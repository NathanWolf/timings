<?php
/*
 * Aikar's Minecraft Timings Parser
 *
 * Written by Aikar <aikar@aikar.co>
 * http://aikar.co
 * http://starlis.com
 *
 * @license MIT
 */
namespace Starlis\Timings\Json;
use Starlis\Timings\FromJson;

class TicksRecord {
    use FromJson;

    /**
     * @index 0
     * @var int
     */
    public $timedTicks;
    /**
     * @index 1
     * @var int
     */
    public $playerTicks;
    /**
     * @index 2
     * @var int
     */
    public $entityTicks;
    /**
     * @index 3
     * @var int
     */
    public $activatedEntityTicks;
    /**
     * @index 4
     * @var int
     */
    public $tileEntityTicks;
}
