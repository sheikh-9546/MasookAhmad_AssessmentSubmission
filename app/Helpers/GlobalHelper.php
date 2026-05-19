<?php

namespace App\Helpers;

use Carbon\Carbon;

trait GlobalHelper
{
    public static function date($date): ?Carbon
    {
        return isset($date) ? Carbon::parse($date) : null;
    }

    public static function changeDateFormat($date, $date_format): string
    {
        return Carbon::parse($date)->format($date_format);
    }

    public static function changeDateFormatAndAddDays($date, $date_format, $days): string
    {
        return Carbon::parse($date)->addDays($days)->format($date_format);
    }

    public static function convertObjectToArray($body)
    {
        return json_decode(json_encode($body), true);
    }
}
