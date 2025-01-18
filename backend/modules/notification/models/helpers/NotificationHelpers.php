<?php
namespace backend\modules\notification\models\helpers;


class NotificationHelpers
{
    /**
     * @param string $time
     * @param string $dateFormat
     * @param string $separator
     * @return string
     */
    public static function getCreatedTimeByTimeSetting(string $time, string $dateFormat = 'Y-m-d H:i:s', string $separator = ":"): string
    {
        $timeArray = explode($separator, $time);

        $hours = $timeArray[0] ?? 0;
        $minutes = $timeArray[1] ?? 0;
        $seconds = $timeArray[2] ?? 0;
        $string = "-0 weeks -0 days -$hours hours -$minutes minutes -$seconds seconds";

        return date($dateFormat, strtotime($string));
    }
}