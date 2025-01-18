<?php

namespace src\helpers;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeZone;
use Yii;

class Date
{
    public static function interval_hour($start, $end)
    {
        $date1 = new DateTime($start);
        $date2 = new DateTime($end);
        $diff = $date2->diff($date1);
        return $diff->h + ($diff->days * 24);
    }

    public static function days($start, $end, $returned = null)
    {
        $date_start = new \DateTime($start);
        $date_end = new \DateTime($end);
        $date_returned = new \DateTime($returned);
        if ($returned == null) {
            $interval = $date_start->diff($date_end);
        } else {
            $interval = $date_start->diff($date_returned);
        }
        return $interval->format('%d');
    }

    public static function datePeriodGridMonth($start, $end)
    {
        $start = new DateTime($start);
        $end = new DateTime($end);
        $interval = $end->diff($start);
        $months = $interval->y * 12 + $interval->m;
        if ($months == 0) {
            return 0;
        }

        $period = new DatePeriod($start, new DateInterval('P1M'), $months);
        $format = 'Y-m-d';
        $result = [];
        foreach ($period as $date) {
            $result[] = $date->format($format);
        }
        return $result;
    }

    public static function datePeriodGridDay($startAt, $endAt)
    {
        if (!$startAt && !$endAt) {
            return [self::date_now()];
        }
        $start = new DateTime($startAt);
        $end = new DateTime($endAt);
        $interval = $end->diff($start);
        $days = $interval->days;
        if ($days == 0) {
            return [$startAt];
        }
        $period = new DatePeriod($start, new DateInterval('P1D'), $days);
        $format = 'Y-m-d';
        $result = [];
        foreach ($period as $date) {
            $result[] = $date->format($format);
        }
        return $result;
    }

    public static function datePeriodGridYear($start, $end)
    {
        $start = new DateTime($start);
        $end = new DateTime($end);

        $years = intval($end->format('Y-m-d')) - intval($start->format('Y-m-d'));
        if ($years == 0) {
            return [];
        }
        $period = new DatePeriod($start, new DateInterval('P1Y'), $years);
        $format = 'Y-m-d';

        $result = [];
        foreach ($period as $date) {
            $result[] = $date->format($format);
        }
        return $result;
    }

    public static function minusPlus($date, $modify)
    {
        $d = new DateTime($date);
        $d->modify($modify);
        return $d->format('Y-m-d H:i:s');
    }

    public static function plusDay($date, $qty)
    {
        $d = new DateTime($date);
        $d->modify("+$qty day");
        return $d->format("Y-m-d");
    }

    public static function date_converter($date)
    {
        $d = explode('.', $date);
        if (isset($d[0]) && isset($d[1]) && isset($d[2])) {
            return $d[2] . '-' . $d[1] . '-' . $d[0];
        }
    }

    public static function dateConverterGoogle($date)
    {
        $d = explode('.', $date);
        if (isset($d[0]) && isset($d[1]) && isset($d[2])) {
            return $d[2] . '-' . $d[1] . '-' . $d[0] . ' 08:00:00';
        }
    }

    public static function dateConverterGoogleBot($date)
    {
        return $date . ' 08:00:00';
    }

    public static function formatGoogle($date)
    {
        $d = new DateTime($date, new DateTimeZone('Europe/Kyiv'));
        return $d->format('Y-m-d\TH:i:s');
//        return $d->format('Y-m-d\TH:i:sP');
    }

    public static function datetime_converter($date)
    {
        return ($date) ? Yii::$app->formatter->asDatetime($date, 'php:Y-m-d H:i:s') : '';
    }

    public static function timestampConverter($timestamp)
    {
        $d = new DateTime();
        $d->setTimestamp($timestamp);
        return $d->format('Y-m-d H:i:s');
    }

    public static function format_date($date)
    {
        return ($date) ? Yii::$app->formatter->asDate($date, 'php:d.m.Y') : '';
    }

    public static function format_datetime($date)
    {
        return ($date) ? Yii::$app->formatter->asDatetime($date, 'php:d.m.Y H:i') : '';
    }

    public static function format_datetime_all($date)
    {
        return ($date) ? Yii::$app->formatter->asDatetime($date, 'php:d.m.Y H:i:s') : '';
    }

    public static function date_now()
    {
        $d = new DateTime('now', new DateTimeZone('Europe/Kyiv'));
        return $d->format('Y-m-d');
    }

    public static function datetime_now()
    {
        $d = new DateTime('now', new DateTimeZone('Europe/Kyiv'));
        return $d->format('Y-m-d H:i:s');
    }

    public static function day_now_week()
    {
        $d = new DateTime('now', new DateTimeZone('Europe/Kyiv'));
        return $d->format('l');
    }

    public static function time_now()
    {
        $d = new DateTime('now', new DateTimeZone('Europe/Kyiv'));
        return $d->format('H:i:s');
    }

    public static function TrueIfStartLessThanEnd($start, $end)
    {
        return new \DateTime($start) < new \DateTime($end);
    }

    public static function trueIfDateNowLess($date)
    {
        return new DateTime(self::date_now()) < new DateTime($date);
    }
}

