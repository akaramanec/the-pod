<?php

namespace backend\modules\customer\models;

use src\helpers\Date;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * https://www.amcharts.com/demos/multiple-value-axes/
 */
class Statistic
{
    public $api;
    public $amCharts = [];
    public $countAllCustomer = 0;
    public $countAllCustomerSubscribed = 0;
    public $countNewCustomer = 0;
    public $countNewCustomerSubscribed = 0;
    public $clickStatistic = [];
    public $clickCount = [];
    public $maxClick = 0;

    public function __construct()
    {
        $this->setPeriodClickStatistic();
        $this->countAllCustomer();
        $this->countAllCustomerSubscribed();
        $this->amCharts();
    }

    public function countAllCustomer()
    {
        $this->countAllCustomer = Yii::$app->db->createCommand("SELECT COUNT(*) FROM bot_customer WHERE status=:status", [
            ':status' => Customer::STATUS_ACTIVE
        ])->queryScalar();
    }

    public function countAllCustomerSubscribed()
    {
        $this->countAllCustomerSubscribed = Yii::$app->db->createCommand("SELECT COUNT(*) FROM bot_customer WHERE status=:status", [
            ':status' => Customer::STATUS_SUBSCRIBED
        ])->queryScalar();
    }

    public function setPeriodClickStatistic()
    {
        foreach (ClickStatistic::statusList() as $key => $name) {
            $this->clickCount[$key] = $this->countClickStatistic($key);
        }
        if ($this->clickCount) {
            $this->maxClick = max($this->clickCount);

            foreach (ClickStatistic::statusList() as $click => $nameClick) {
                $this->clickStatistic[] = [
                    'name' => $nameClick,
                    'percent' => $this->percent($this->clickCount[$click]),
                    'count' => $this->clickCount[$click],
                ];
            }
        }
        ArrayHelper::multisort($this->clickStatistic, 'count', SORT_DESC);
    }

    private function percent($count)
    {
        if ($count) {
            $x = ($count / $this->maxClick) * 100;
            return (int)$x;
        } else {
            return 0;
        }
    }

    public function countClickStatistic($click)
    {
        return ClickStatistic::find()
            ->where(['click' => $click])
            ->andWhere(['between', 'created_at', self::statisticDateFrom() . ' 00:01:00', self::statisticDateTo() . ' 23:59:00'])
            ->count();
    }

    private function countCustomerPerDay($date)
    {
        return Yii::$app->db->createCommand("SELECT COUNT(*) FROM bot_customer WHERE DATE_FORMAT(created_at, '%Y-%m-%d')=:date AND status=:status",
            [
                ':date' => $date,
                ':status' => Customer::STATUS_ACTIVE
            ])->queryScalar();
    }

    private function countCustomerSubscribed($date)
    {
        return Yii::$app->db->createCommand("SELECT COUNT(*) FROM bot_customer WHERE DATE_FORMAT(created_at, '%Y-%m-%d')=:date AND status=:status",
            [
                ':date' => $date,
                ':status' => Customer::STATUS_SUBSCRIBED
            ])->queryScalar();
    }

    public function amCharts()
    {
        $date = Date::datePeriodGridDay(self::statisticDateFrom(), self::statisticDateTo());
        foreach ($date as $item) {
            $countCustomerPerDay = $this->countCustomerPerDay($item);
            $this->countNewCustomer += $countCustomerPerDay;

            $countCustomerSubscribed = $this->countCustomerSubscribed($item);
            $this->countNewCustomerSubscribed += $countCustomerSubscribed;

            $this->amCharts[] = [
                'date' => $item,
                'customer_active' => $countCustomerPerDay,
                'customer_phone' => $countCustomerSubscribed,
            ];
        }
        $this->amCharts = json_encode($this->amCharts);
    }

    public static function statisticDateFrom()
    {
        if (isset($_SESSION['date_from'])) {
            return $_SESSION['date_from'];
        } else {
            return Date::minusPlus(Date::date_now(), '-30 day');
        }
    }

    public static function statisticDateTo()
    {
        if (isset($_SESSION['date_to'])) {
            return $_SESSION['date_to'];
        } else {
            return Date::minusPlus(Date::date_now(), '+1 day');
        }
    }

    public static function statisticDate()
    {
        return Date::format_date(self::statisticDateFrom()) . ' - ' . Date::format_date(self::statisticDateTo());
    }
}
