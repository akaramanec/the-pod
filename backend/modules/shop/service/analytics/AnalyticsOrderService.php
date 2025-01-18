<?php

namespace backend\modules\shop\service\analytics;


use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Order;
use backend\modules\shop\service\analytics\components\general\LVTOrder;
use backend\modules\shop\service\analytics\components\general\UsersAll;
use backend\modules\shop\service\analytics\components\general\UsersUnique;
use backend\modules\shop\service\analytics\components\general\UsersUnsubscribed;
use backend\modules\shop\service\analytics\components\newsletter\AnalyticsNewsletterItemInterface;
use backend\modules\shop\service\analytics\components\newsletter\MoreFiveOrder;
use backend\modules\shop\service\analytics\components\newsletter\OneOrder;
use backend\modules\shop\service\analytics\components\newsletter\ToFiveOrder;
use backend\modules\shop\service\analytics\components\orderProcessing\ManagerOrderProcessing;
use backend\modules\shop\service\analytics\components\status\AnalyticsStatusItemInterface;
use backend\modules\shop\service\analytics\components\status\CanceledStatus;
use backend\modules\shop\service\analytics\components\status\ConfirmedStatus;
use backend\modules\shop\service\analytics\components\status\ConfirmedViewedStatus;
use backend\modules\shop\service\analytics\components\status\InProcessingStatus;
use backend\modules\shop\service\analytics\components\status\InWorkStatus;
use backend\modules\shop\service\analytics\components\status\SuccessStatus;
use backend\modules\shop\service\analytics\components\status\UnconfirmedStatus;
use backend\modules\shop\service\analytics\enum\AnalyticsGeneralTypeEnum;
use backend\modules\shop\service\analytics\enum\AnalyticsNewsletterIndicatorTypeEnum;
use backend\modules\shop\service\analytics\exception\AnalyticsException;
use backend\modules\shop\service\analytics\components\general\AnalyticsGeneralItemInterface;
use backend\modules\shop\service\analytics\components\general\AverageCountOrder;
use backend\modules\shop\service\analytics\components\general\AverageSumOrder;
use backend\modules\shop\service\analytics\components\general\MaxSumOrder;
use backend\modules\shop\service\analytics\components\general\SuccessOrder;
use backend\modules\shop\service\analytics\components\general\SumSuccessOrder;
use src\helpers\Date;
use Yii;
use yii\helpers\Json;

class AnalyticsOrderService
{
    /** @var string $dateFrom */
    protected $dateFrom;

    /** @var string $dateTo */
    protected $dateTo;

    /** @var AnalyticsGeneralItemInterface[] $general */
    protected $general;

    /** @var AnalyticsStatusItemInterface[] $status */
    protected $status;

    /** @var ManagerOrderProcessing */
    protected $managerOrderProcessing;

    /** @var AnalyticsNewsletterItemInterface[] */
    protected $newsletterIndicators;

    /** @var array $dataChart ['datesList' => [], 'countOrdersByDates' => []] */
    protected $dataChart;

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @throws \yii\db\Exception
     */
    public function __construct(string $dateFrom, string $dateTo)
    {
        $dateFormat = 'Y-m-d H:i:s';
        $this->dateFrom = date($dateFormat, strtotime($dateFrom));
        $this->dateTo = date($dateFormat, strtotime($dateTo));
        $this->initGeneral();
        $this->initStatus();
        $this->initManagerTracking();
        $this->initNewsletterIndicators();
        $this->initDataCarts();
    }

    /**
     * @param string $generalType
     * @return AnalyticsGeneralItemInterface
     * @throws AnalyticsException
     */
    public function getGeneralIndicators(string $generalType): AnalyticsGeneralItemInterface
    {
        if (isset($this->general[$generalType])) {
            return $this->general[$generalType];
        } else {
            throw new AnalyticsException(sprintf("Not found AnalyticsGeneral by type: %s", $generalType));
        }
    }

    /**
     * @param string $indicatorType
     * @return AnalyticsNewsletterItemInterface
     * @throws AnalyticsException
     */
    public function getNewsletterIndicators(string $indicatorType): AnalyticsNewsletterItemInterface
    {
        if (isset($this->newsletterIndicators[$indicatorType])) {
            return $this->newsletterIndicators[$indicatorType];
        } else {
            throw new AnalyticsException(sprintf("Not found AnalyticsNewsletterIndicators by type: %s", $indicatorType));
        }
    }

    /**
     * @return AnalyticsStatusItemInterface[]
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @return ManagerOrderProcessing
     */
    public function getManagerOrderProcessing(): ManagerOrderProcessing
    {
        return $this->managerOrderProcessing;
    }

    /**
     * @return string
     */
    public function getChartJs(): string
    {
        return $this->generateJsChart($this->dataChart);
    }

    public function getDateFrom()
    {
        if (isset($this->dateFrom)) {
            return $this->dateFrom;
        } else {
            return Date::minusPlus(Date::date_now(), '-30 day');
        }
    }

    public function getDateTo()
    {
        if (isset($this->dateTo)) {
            return $this->dateTo;
        } else {
            return Date::minusPlus(Date::date_now(), '+1 day');
        }
    }

    public function getDateRage()
    {
        return Date::format_date($this->getDateFrom()) . ' - ' . Date::format_date($this->getDateTo());
    }

    /**
     * @throws \yii\db\Exception
     */
    private function initGeneral()
    {
        $sql = <<<SQL
SELECT SUM(sh_o.cache_sum_total) AS total_sum,
       COUNT(*) AS count_order,
       (SELECT COUNT(*) FROM `shop_order` AS sho WHERE  sho.status != :statusNew AND sho.updated_at BETWEEN :dateFrom AND :dateTo)  AS all_count_order,
       MAX(sh_o.cache_sum_total) AS max_sum,
       (SELECT COUNT(*) FROM `bot_customer` WHERE bot_customer.status = :bot_customer_status) AS count_customer,                                            
       (SELECT COUNT(*) FROM `bot_customer` WHERE bot_customer.status = :bot_customer_unsubscribed_status AND bot_customer.updated_at BETWEEN :dateFrom AND :dateTo) AS unsubscribed_customer,
       COUNT(DISTINCT (sh_o.customer_id)) AS lvt_customer
FROM shop_order AS sh_o
WHERE sh_o.status = :orderStatus AND sh_o.updated_at BETWEEN :dateFrom AND :dateTo

SQL;
        $params = [
            ':orderStatus' => Order::STATUS_CLOSE_SUCCESS,
            ':statusNew' => Order::STATUS_NEW,
            ':dateFrom' => $this->dateFrom,
            ':dateTo' => $this->dateTo,
            ':bot_customer_status' => Customer::STATUS_ACTIVE,
            ':bot_customer_unsubscribed_status' => Customer::STATUS_UNSUBSCRIBED
        ];

        $result = Yii::$app->db->createCommand($sql)
            ->bindValues($params)
            ->queryOne();

        $this->general[AnalyticsGeneralTypeEnum::AVERAGE_SUM_ORDER] = new AverageSumOrder(floatval($result['total_sum']), intval($result['count_order']));
        $this->general[AnalyticsGeneralTypeEnum::MAX_SUM_ORDER] = new MaxSumOrder(floatval($result['max_sum']));
        $this->general[AnalyticsGeneralTypeEnum::AVERAGE_COUNT_ORDER] = new AverageCountOrder(intval($result['count_order']), intval($result['count_customer']));
        $this->general[AnalyticsGeneralTypeEnum::LVT_ORDER] = new LVTOrder(intval($result['count_order']), intval($result['lvt_customer']));
        $this->general[AnalyticsGeneralTypeEnum::SUCCESS_ORDER] = new SuccessOrder(intval($result['count_order']), intval($result['all_count_order']));
        $this->general[AnalyticsGeneralTypeEnum::SUM_SUCCESS_ORDER] = new SumSuccessOrder(floatval($result['total_sum']));
        $this->general[AnalyticsGeneralTypeEnum::USERS_ALL] = new UsersAll(intval($result['count_customer']));
        $this->general[AnalyticsGeneralTypeEnum::USERS_UNIQUE] = new UsersUnique(intval($result['lvt_customer']));
        $this->general[AnalyticsGeneralTypeEnum::USERS_UNSUBSCRIBED] = new UsersUnsubscribed(intval($result['unsubscribed_customer']));



    }

    /**
     * @throws \yii\db\Exception
     */
    private function initStatus()
    {
        $sql = <<<SQL
SELECT COUNT(CASE WHEN sh_o.status = :statusSuccess THEN 1 END) AS success_count,
       COUNT(CASE WHEN sh_o.status = :statusInWork THEN 1 END) AS in_work_count,
       COUNT(CASE WHEN sh_o.status = :statusCanceled THEN 1 END) AS canceled_count,
       COUNT(CASE WHEN sh_o.status = :statusInProcessing THEN 1 END) AS in_processing_count,
       COUNT(CASE WHEN sh_o.status = :statusConfirmed THEN 1 END) AS confirmed_count,
       COUNT(CASE WHEN sh_o.status = :statusConfirmedViewed THEN 1 END) AS confirmed_viewed_count,
       COUNT(CASE WHEN sh_o.status = :statusUnconfirmed THEN 1 END) AS unconfirmed_count,
       COUNT(CASE WHEN sh_o.status != :statusNew THEN 1 END) AS all_count
FROM shop_order AS sh_o
WHERE sh_o.updated_at BETWEEN :dateFrom AND :dateTo
SQL;

        $params = [
            ':statusSuccess' => Order::STATUS_CLOSE_SUCCESS,
            ':statusInWork' => Order::STATUS_IN_WORK,
            ':statusCanceled' => Order::STATUS_CLOSE_CANCELED,
            ':statusNew' => Order::STATUS_NEW,
            ':statusInProcessing' => Order::STATUS_IN_PROCESSING,
            ':statusConfirmed' => Order::STATUS_CONFIRMED,
            ':statusConfirmedViewed' => Order::STATUS_CONFIRMED_AND_VIEWED,
            ':statusUnconfirmed' => Order::STATUS_UNCONFIRMED,
            ':dateFrom' => $this->dateFrom,
            ':dateTo' => $this->dateTo
        ];

        $result = Yii::$app->db->createCommand($sql)
            ->bindValues($params)
            ->queryOne();

        $this->status[] = new SuccessStatus(intval($result['success_count']), intval($result['all_count']));
        $this->status[] = new InWorkStatus(intval($result['in_work_count']), intval($result['all_count']));
        $this->status[] = new CanceledStatus(intval($result['canceled_count']), intval($result['all_count']));
        $this->status[] = new InProcessingStatus(intval($result['in_processing_count']), intval($result['all_count']));
        $this->status[] = new ConfirmedStatus(intval($result['confirmed_count']), intval($result['all_count']));
        $this->status[] = new ConfirmedViewedStatus(intval($result['confirmed_viewed_count']), intval($result['all_count']));
        $this->status[] = new UnconfirmedStatus(intval($result['unconfirmed_count']), intval($result['all_count']));

    }

    /**
     * @throws \yii\db\Exception
     */
    private function initManagerTracking()
    {
        $sql = <<<SQL
SELECT manager.id AS mangerId, 
       CONCAT_WS(' ', manager.surname, manager.name) AS managerName,
        (
           SELECT COUNT(sh_o.id)
           FROM shop_order AS sh_o 
           WHERE sh_o.manager_id = manager.id 
             AND sh_o.status != :statusNew
             AND sh_o.updated_at BETWEEN :dateFrom AND :dateTo ) AS countAllOrders,
        (
           SELECT COUNT(sh_o.id)
           FROM shop_order AS sh_o 
           WHERE sh_o.manager_id = manager.id 
             AND sh_o.status = :statusCanceled
             AND sh_o.updated_at BETWEEN :dateFrom AND :dateTo ) AS countCanceledOrders,
       SUM(CASE WHEN tracking.old_status = :statusSuccess THEN tracking.step_time END) AS timeSuccessStatus,
       SUM(CASE WHEN tracking.old_status = :statusInWork THEN tracking.step_time END) AS timeInWorkStatus,
       SUM(CASE WHEN tracking.old_status = :statusProcessing THEN tracking.step_time END) AS timeInProcessingStatus,
       SUM(CASE WHEN tracking.old_status = :statusSuccess THEN 1 END) AS countInSuccessStatus,
       SUM(CASE WHEN tracking.old_status = :statusInWork THEN 1 END) AS countInInWorkStatus,
       SUM(CASE WHEN tracking.old_status = :statusProcessing THEN 1 END) AS countInInProcessingStatus
FROM `auth_admin` as manager
    LEFT JOIN `shop_order_status_tracking` AS tracking ON tracking.manager_id = manager.id
WHERE tracking.created_at BETWEEN :dateFrom AND :dateTo
GROUP BY manager.id
SQL;

        $params = [
            ':statusSuccess' => Order::STATUS_CLOSE_SUCCESS,
            ':statusInWork' => Order::STATUS_IN_WORK,
            ':statusProcessing' => Order::STATUS_IN_PROCESSING,
            ':statusCanceled' => Order::STATUS_CLOSE_CANCELED,
            ':statusNew' => Order::STATUS_NEW,
            ':dateFrom' => $this->dateFrom,
            ':dateTo' => $this->dateTo
        ];
        $result = Yii::$app->db->createCommand($sql)
            ->bindValues($params)
            ->queryAll();

        $this->managerOrderProcessing = new ManagerOrderProcessing($result);

    }

    /**
     * @throws \yii\db\Exception
     */
    private function initNewsletterIndicators()
    {
//        $sqlItem = <<<SQL
//SELECT
//	customer.id,
//    COUNT(sh_o.id) AS count_order
//FROM `bot_customer` AS customer
//LEFT JOIN `shop_order` AS sh_o ON sh_o.customer_id = customer.id
//WHERE sh_o.created_at BETWEEN '2021-08-28 23:07:13' AND '2021-09-27 23:07:13' AND sh_o.status = 7
//GROUP BY customer.id
//
//HAVING count_order > 5
//SQL;

        $sql = <<<SQL
SELECT COUNT(CASE WHEN 
       (SELECT COUNT(sho.id) FROM shop_order AS sho WHERE sho.status = :statusSuccess AND sho.customer_id = customer.id AND  sho.created_at BETWEEN :dateFrom AND :dateTo )
           = 1 THEN 1 END) AS oneOrder,
       
       COUNT(CASE WHEN 
           (SELECT COUNT(sho.id) FROM shop_order AS sho WHERE sho.status = :statusSuccess AND sho.customer_id = customer.id AND sho.created_at BETWEEN :dateFrom AND :dateTo )
               BETWEEN 2 AND 5 THEN 1 END) AS toFiveOrders,
       
       COUNT(CASE WHEN 
           (SELECT COUNT(sho.id) FROM shop_order AS sho WHERE sho.status = :statusSuccess AND sho.customer_id = customer.id AND sho.created_at BETWEEN :dateFrom AND :dateTo )
               > 5 THEN 1 END) AS moreFiveOrders

FROM bot_customer AS customer;
SQL;
        $params = [
            ':statusSuccess' => Order::STATUS_CLOSE_SUCCESS,
            ':dateFrom' => $this->dateFrom,
            ':dateTo' => $this->dateTo
        ];

        $result = Yii::$app->db->createCommand($sql)
            ->bindValues($params)
            ->queryOne();

        $this->newsletterIndicators[AnalyticsNewsletterIndicatorTypeEnum::ONE] = new OneOrder($result['oneOrder'], $this->dateFrom, $this->dateTo);
        $this->newsletterIndicators[AnalyticsNewsletterIndicatorTypeEnum::TO_FIVE] = new ToFiveOrder($result['toFiveOrders'], $this->dateFrom, $this->dateTo);
        $this->newsletterIndicators[AnalyticsNewsletterIndicatorTypeEnum::MORE_FIVE] = new MoreFiveOrder($result['moreFiveOrders'], $this->dateFrom, $this->dateTo);
    }

    /**
     * @throws \yii\db\Exception
     */
    private function initDataCarts()
    {
        $dates = Date::datePeriodGridDay($this->dateFrom, $this->dateTo);
        $bindParams = [
            ':statusNew' => Order::STATUS_NEW,
            ':dateFrom' => $this->dateFrom,
            ':dateTo' => $this->dateTo
        ];
        $sql = "SELECT ";
        foreach ($dates as $key => $date) {
            if ($key > 0) {
                $sql .= ",\n";
            }
            $sql .= "COUNT(CASE WHEN DATE_FORMAT(sh_o.created_at, '%Y-%m-%d')= :date$key THEN 1 END) AS date$key";
            $bindParams[":date$key"] = $date;
        }

        $sql .= "\nFROM shop_order AS sh_o\nWHERE sh_o.status != :statusNew AND sh_o.created_at BETWEEN :dateFrom AND :dateTo;";
        $result = Yii::$app->db->createCommand($sql)
            ->bindValues($bindParams)
            ->queryOne();

        $this->dataChart = ['datesList' => $dates, 'countOrdersByDates' => array_values($result)];

    }



    /**
     * @return string
     */
    private function generateJsChart(array $dataCarts): string
    {
        $labels = Json::encode(array_map(function ($date) {
            return date('d.m', strtotime($date));
        },$dataCarts['datesList']));
        $stepSize = max($dataCarts['countOrdersByDates']) <= 15 ? 1 : 'false';
        $dataChartAppeal = Json::encode($dataCarts['countOrdersByDates']);
        $js = <<<JS
        var observer = new MutationObserver(function (mutations) {
            let parent = document.querySelector('#chart_order');
            if (parent !== null) {
                initChart();
                observer.disconnect();
            }
        });

        observer.observe(document, {attributes: false, childList: true, characterData: false, subtree: true});
$( document ).ready(function () {
    initChart();
});
function initChart() {
    let ctx = $("#chart_order");
    let stepSize = $stepSize;
    let dataLabels = $labels;
    let dataChartAppeal = $dataChartAppeal;
    let chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            display: false,
            position: "bottom"
        },
        tooltips: {
            mode: 'index',
            intersect: false
        },
        hover: {mode: "label"},
        scales: {
            xAxes: [{
                display: true,
                gridLines: {
                    color: "#f3f3f3",
                    drawTicks: false
                },
                scaleLabel: {
                    display: true,
                    labelString: "Дата"
                },
                 ticks: { 
                    padding: 5,
                    minRotation: 90,               
                    display: true,
                    fontSize: 11
                }
            }],
            yAxes: [{
                display: true,
                gridLines: {
                    color: "#f3f3f3",
                    drawTicks: false
                },
                scaleLabel: {
                    display: false,
                    labelString: "Количество продаж"
                },
                ticks: {
                    stepSize: stepSize,
                    beginAtZero:true,
                    fontSize: 11
                }
            }]
        },
        title: {
            display: true,
            text: "Количество продаж"
        }
    };
    let chartData = {
        labels: dataLabels,
        datasets: [{
            label: "Количество продаж",
            data: dataChartAppeal,          
            backgroundColor: "rgba(94, 162, 235, 1)",
            pointBorderColor: "#1e9ff2",
            pointBackgroundColor: "#FFF",
            pointBorderWidth: 1,
            pointHoverBorderWidth: 1,
            pointRadius: 3
        }]
    };
    let config = {
        type: "bar",
        options: chartOptions,
        data: chartData
    };
    new Chart(ctx, config);
}
JS;
        return $js;
    }

}