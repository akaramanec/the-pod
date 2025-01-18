<?php

namespace src\helpers;

use src\helpers\Date;
use yii\helpers\Html;

class DatePeriodSelectorHelper
{
    const PERIOD_WHOLE_PERIOD = 'whole_period';
    const PERIOD_HALF_YEAR = 'half_year';
    const PERIOD_MOON = 'moon';

    /**
     * @return string
     */
    public static function selectPeriod(string $url = ''): string
    {
        $noSelectedTitle = 'Выберите период';

        $titleList = self::getPeriodTitleList();

        $period = $_GET['period'] ?? '';

        $selected = $titleList[$period] ?? $noSelectedTitle;

        $dropdownList = Html::a($noSelectedTitle, [$url], ['class' => ['dropdown-item']]);

        foreach ($titleList as $get_param => $title) {
            $dropdownList .= Html::a($title, [$url, 'period' => $get_param], ['class' => ['dropdown-item']]);
        }

        return <<<HTML
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" 
            type="button" id="dropdownMenuButton" 
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 100%">
                {$selected}
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                {$dropdownList}
            </div>
        </div>
HTML;

    }

    /**
     * @return string[]
     */
    public static function getPeriodList(): array
    {
        return [
            self::PERIOD_WHOLE_PERIOD,
            self::PERIOD_HALF_YEAR,
            self::PERIOD_MOON
        ];
    }

    /**
     * @return string[]
     */
    public static function getPeriodTitleList(): array
    {
        return [
            self::PERIOD_WHOLE_PERIOD => 'За весь период',
            self::PERIOD_HALF_YEAR => 'За пол года',
            self::PERIOD_MOON => 'За месяц'
        ];
    }

    public static function getDateRage($dateFrom = null, $dateTo = null)
    {
        return Date::format_date(self::getDateFrom($dateFrom)) . ' - ' . Date::format_date(self::getDateTo($dateTo));
    }

    public static function getDateFrom($dateFrom = null)
    {
        return self::strToTime($dateFrom) ?? Date::minusPlus(Date::date_now(), '-30 day');
    }

    public static function getDateTo($dateTo = null)
    {
        return self::strToTime($dateTo) ?? Date::minusPlus(Date::date_now(), '+1 day');
    }

    public static function getPeriod(): array
    {
        $date_range = [
            'dateFrom' => "-30 days",
            'dateTo' => 'now'
        ];

        $date_from = \Yii::$app->request->get('dateFrom');
        $date_to = \Yii::$app->request->get('dateTo');
        if (!empty($date_from) && !empty($date_to)) {
            $date_range['dateFrom'] = $date_from;
            $date_range['dateTo'] = $date_to;
        }

        $period = \Yii::$app->request->get('period');
        switch ($period) {
            case self::PERIOD_WHOLE_PERIOD:
                $sql = <<<SQL
                        SELECT MIN(c.updated_at) AS dateFrom,
                               MAX(c.updated_at) AS dateTo
                        FROM dev.bot_customer AS c
SQL;
                $date_range = \Yii::$app->db->createCommand($sql)->queryOne();
                break;
            case self::PERIOD_HALF_YEAR:
                $date_range['dateFrom'] = "-182 days";
                break;
            case self::PERIOD_MOON:
                break;
        }

        return $date_range;
    }

    public static function strToTime($date)
    {
        $dateFormat = 'Y-m-d H:i:s';
        return date($dateFormat, strtotime($date));
    }
}