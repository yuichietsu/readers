<?php

namespace Menrui\Reader;

use Iterator;
use Menrui\Reader;
use SimpleXMLElement;

class MameDat extends Reader
{
    public static function readFile(string $file): Iterator
    {
        $xml = simplexml_load_file($file);
        return $xml->game ?: $xml->machine;
    }

    public static function isOriginal(SimpleXMLElement $game): bool
    {
        return !$game['cloneof'];
    }

    public static function isArcade(SimpleXMLElement $game): bool
    {
        return !$game->softwarelist;
    }

    public static function isNoChd(SimpleXMLElement $game): bool
    {
        return !$game->disk;
    }

    public static function isPlayable(SimpleXMLElement $game): bool
    {
        $isBios    = (string)$game['isbios'] === 'yes';
        $isDevice  = (string)$game['isdevice'] === 'yes';
        $isRunnable = (string)$game['runnable'] !== 'no';
        $isWorking = (string)$game->driver['status'] !== 'preliminary';
        return $isRunnable && $isWorking && !$isDevice && !$isBios;
    }

    public static function isMahjong(SimpleXMLElement $game): bool
    {
        foreach ($game->input as $input) {
            //$coins = (int)$input['coins'];
            foreach ($input->control as $ctrl) {
                if ((string)$ctrl['type'] === 'mahjong') {
                    $player  = (int)$ctrl['player'];
                    $buttons = (int)$ctrl['buttons'];
                    if ($player <= 1 && 19 <= $buttons && $buttons !== 30) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
