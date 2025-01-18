<?php

namespace backend\modules\bot\telegram;

use Yii;

class Calendar
{
    public static function next($date_from = null)
    {
        if (!isset(Yii::$app->tm->data->value) || !isset(Yii::$app->tm->data->main_action)) {
            return [];
        }

        $date = explode("-", Yii::$app->tm->data->value);
        $year = (int)$date[0];
        $month = (int)$date[1];
        $keyboard = self::create_calendar(Yii::$app->tm->data->main_action, $year, $month + 1, $date_from, 'next');
        return json_encode($keyboard);
    }

    public static function prev($date_from = null)
    {
        if (!isset(Yii::$app->tm->data->value) || !isset(Yii::$app->tm->data->main_action)) {
            return [];
        }

        $date = explode("-", Yii::$app->tm->data->value);
        $year = (int)$date[0];
        $month = (int)$date[1];
        if ($month == 1) {
            $month = 1;
        } else {
            $month -= 1;
        }
        $keyboard = self::create_calendar(Yii::$app->tm->data->main_action, $year, $month, $date_from, 'prev');
        return json_encode($keyboard);
    }

    public static function InlineKeyboardButton($text, $type, $data)
    {
        $button = [
            'text' => $text,
            $type => $data
        ];
        return $button;
    }

    public static function InlineKeyboardMarkup($keyboard)
    {
        return ['inline_keyboard' => $keyboard];
    }

    private static function monthCalendar($year, $month)
    {
        $month_stamp = mktime(0, 0, 0, $month, 1, $year);
        $day_count = date("t", $month_stamp);
        $weekday = date("w", $month_stamp);
        if ($weekday == 0) {
            $weekday = 7;
        }
        $start = -($weekday - 2);
        $last = ($day_count + $weekday - 1) % 7;
        if ($last == 0) {
            $end = $day_count;
        } else {
            $end = $day_count + 7 - $last;
        }
        $i = 1;
        $week = [];
        $calendar = [];
        for ($day = $start; $day <= $end; $day++) {
            if ($day < 1 || $day > $day_count) {
                array_push($week, 0);
            } else {
                array_push($week, $day);
            }
            if (!($i % 7)) {
                array_push($calendar, $week);
                $week = [];
            }
            $i++;
        }
        return $calendar;
    }

    public static function create_calendar($action, $year = null, $month = null, $date_from = null, $nextPrev = null)
    {
        $month_names = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
        if (!isset($year) OR $year < 1970 OR $year > 2037) {
            $year = date("Y");
        }
        if (!isset($month)) {
            $month = date("n");
        }
        if ($month < 1 OR $month > 12) {
            $year = date("Y", mktime(0, 0, 0, $month, 1, $year));
            $month = date("n", mktime(0, 0, 0, $month, 1, $year));
        }

        if ($date_from && $nextPrev == null) {
            $dateFromArray = explode('-', $date_from);
            $year = $dateFromArray[0];
            $month = $dateFromArray[1];
        }
        $data_ignore = 'none';
        $keyboard = [];
        #First row - Month and Year
        $row = [];
        array_push($row, self::InlineKeyboardButton($month_names[$month - 1] . " " . $year, 'callback_data', $data_ignore));
        array_push($keyboard, $row);
        #Second row - Week Days
        $row = [];
        foreach (['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'] as $day) {
            array_push($row, self::InlineKeyboardButton($day, 'callback_data', $data_ignore));
        }
        array_push($keyboard, $row);

        $calendar = self::monthcalendar($year, $month);

        foreach ($calendar as $week) {
            $row = [];
            foreach ($week as $day) {
                if ($date_from) {
                    $dateFromArray = explode('-', $date_from);
                    if ($month == $dateFromArray[1] && $day == $dateFromArray[2]) {
                        $date_from_action = json_encode([
                            'action' => $action,
                            'value' => $date_from,
                        ]);
                        array_push($row, self::InlineKeyboardButton('✅', 'callback_data', $date_from_action));
                        continue;
                    }
                }
                if ($day == 0) {
                    array_push($row, self::InlineKeyboardButton(' ', 'callback_data', $data_ignore));
                } else {
                    $date = $year . '-' . $month . '-' . $day;
                    $callback_data = json_encode([
                        'action' => $action,
                        'value' => $date,
                    ]);
                    if ($date_from && strtotime($date) < strtotime($date_from)) {
                        array_push($row, self::InlineKeyboardButton(self::dayU($day), 'callback_data', $callback_data));
                        continue;
                    }
                    array_push($row, self::InlineKeyboardButton($day, 'callback_data', $callback_data));
                }
            }
            array_push($keyboard, $row);
        }
        $row = [];
        $callback_data = json_encode([
            'action' => '/prev',
            'value' => $year . '-' . $month,
            'main_action' => $action,
        ]);
        array_push($row, self::InlineKeyboardButton("<", 'callback_data', $callback_data));
        $callback_data = json_encode([
            'action' => '/next',
            'value' => $year . '-' . $month,
            'main_action' => $action,
        ]);
        array_push($row, self::InlineKeyboardButton(">", 'callback_data', $callback_data));
        array_push($keyboard, $row);
        return self::InlineKeyboardMarkup($keyboard);
    }

    public static function dayU($d)
    {
        $data = [
            1 => ' 1̶',
            2 => ' 2̶',
            3 => ' 3̶',
            4 => ' 4̶',
            5 => ' 5̶',
            6 => ' 6̶',
            7 => ' 7̶',
            8 => ' 8̶',
            9 => ' 9̶',
            10 => ' 1̶0̶',
            11 => ' 1̶1̶',
            12 => ' 1̶2̶',
            13 => ' 1̶3̶',
            14 => ' 1̶4̶',
            15 => ' 1̶5̶',
            16 => ' 1̶6̶',
            17 => ' 1̶7̶',
            18 => ' 1̶8̶',
            19 => ' 1̶9̶',
            20 => ' 2̶0̶',
            21 => ' 2̶1̶',
            22 => ' 2̶2̶',
            23 => ' 2̶3̶',
            24 => ' 2̶4̶',
            25 => ' 2̶5̶',
            26 => ' 2̶6̶',
            27 => ' 2̶7̶',
            28 => ' 2̶8̶',
            29 => ' 2̶9̶',
            30 => ' 3̶0̶',
            31 => ' 3̶1̶'
        ];
        return $data[$d];
    }


}
