<?php

namespace Polygontech\NagadDisbursement;

use Carbon\Carbon;

/**
 * @internal
 */
class Helpers
{
    private static string $timeFormatter = 'YmdHis';

    public static function formatTime(Carbon $time)
    {
        return $time->format(self::$timeFormatter); // date('YmdHis', mktime(0, 0, 0));
    }

    public static function strToTime(string $time)
    {
        return Carbon::createFromFormat(self::$timeFormatter, $time);
    }

    public static function generateRandomString($length = 20)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
