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
namespace Starlis\Timings;

use Starlis\Timings\Json\TimingHandler;
use Starlis\Timings\Json\TimingsMaster;

class Template {
    use Singleton;
    public $history;
    public $js = array();
    public $tpsData;
    public $lagData;
    /**
     * @var TimingHandler[]
     */
    public $handlerData;
    public static function render() {
        require "template/index.php";
    }

    public static function loadData() {
        $timings = Timings::getInstance();
        $timings->loadData();
        $data = TimingsMaster::getInstance();

        $tpl = self::getInstance();

        $ranges = [];

        foreach ($data->data as $history) {
            $ranges[] = $history->start;
            $ranges[] = $history->end;
        }

        $last = count($ranges)-1;

        $tpl->js['ranges']   = $ranges;
        $tpl->js['start']    = $start = (!empty($_GET['start']) ? intval($_GET['start']) : $ranges[$last-1]);
        $tpl->js['end']      = $end   = (!empty($_GET['end'])   ? intval($_GET['end'])   : $ranges[$last]);


        /**
         * @var TimingHandler[] $handlerData
         */
        $handlerData = [];
        $lagData     = [];
        $tpsData     = [];
        $tentData    = [];
        $entData     = [];
        $aentData    = [];
        $chunkData   = [];
        $playerData  = [];
        $timestamps  = [];

        $max = 0;
        foreach ($data->data as $history) {
            $tileEntities = 0;
            $entities = 0;
            $chunks = 0;
            $players = 0;
            foreach ($history->worldData as $world) {
                foreach ($world->chunks as $chunk) {
                    $chunks++;
                }
            }

            foreach ($history->minuteReports as $mp) {
                $total = $mp->fullServerTick->total;
                $lag = $mp->fullServerTick->lagTotal;
                $max = max($total, $max);

                $timestamps[] = $mp->time;
                $tpsData[] = $mp->tps > 19.85 ? 20 : $mp->tps;
                $lagData[] = $lag;
                $chunkData[] = $chunks;
                $entData[] = $mp->ticks->entityTicks / $mp->ticks->timedTicks;
                $playerData[] = $mp->ticks->playerTicks / $mp->ticks->timedTicks;
                $aentData[] = $mp->ticks->activatedEntityTicks / $mp->ticks->timedTicks;
                $tentData[] = $mp->ticks->tileEntityTicks / $mp->ticks->timedTicks;
            }

            if ($history->start >= $start && $history->end <= $end) {
                foreach ($history->handlers as $handler) {
                    $id = $handler->id->id;
                    if (!isset($handlerData[$id])) {
                        $handlerData[$id] = clone $handler;
                        $handlerData[$id]->mergedCount = 1;
                    } else {
                        $handlerData[$id]->addDataFromHandler($handler);
                    }
                }
            }
        }

        $tpl->handlerData    = $handlerData;
        $tpl->js['stamps']   = $timestamps;
        $tpl->js['maxTime']  = $max;
        $tpl->js['chunkData']= $chunkData;
        $tpl->js['entData']  = $entData;
        $tpl->js['aentData'] = $aentData;
        $tpl->js['tentData'] = $tentData;
        $tpl->js['plaData']  = $playerData;
        $tpl->js['lagData']  = $lagData;
        $tpl->js['tpsData']  = $tpsData;
        $tpl->js['id']       = $timings->id;
        $tpl->lagData        = $lagData;
        $tpl->tpsData        = $tpsData;
    }

    public function getData() {
        return json_encode($this->js);
    }
} 
