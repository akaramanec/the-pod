<?php

namespace backend\modules\shop\controllers;

use backend\modules\shop\service\analytics\AnalyticsOrderService;
use src\helpers\DatePeriodSelectorHelper;
use yii\filters\VerbFilter;

class AnalyticsController extends \yii\web\Controller
{
    /**
     * @return array[]
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dateRange = $this->getPeriod();
        $orderAnalytics = new AnalyticsOrderService($dateRange['dateFrom'], $dateRange['dateTo']);
        return $this->render('index', [
            'orderAnalytics' => $orderAnalytics
        ]);
    }

    public function actionFullPeriod()
    {
        $sql = <<<SQL
SELECT MIN(sh_o.updated_at) AS dateFrom,
       MAX(sh_o.updated_at) AS dateTo
FROM shop_order AS sh_o
SQL;
        $date_range = \Yii::$app->db->createCommand($sql)->queryOne();
        return $this->redirect(array_merge(['index'], $date_range));
    }

    private function getPeriod(): array
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
            case DatePeriodSelectorHelper::PERIOD_WHOLE_PERIOD:
                $sql = <<<SQL
                        SELECT MIN(sh_o.updated_at) AS dateFrom,
                               MAX(sh_o.updated_at) AS dateTo
                        FROM shop_order AS sh_o
SQL;
                $date_range = \Yii::$app->db->createCommand($sql)->queryOne();
                break;
            case DatePeriodSelectorHelper::PERIOD_HALF_YEAR:
                $date_range['dateFrom'] = "-182 days";
                break;
            case DatePeriodSelectorHelper::PERIOD_MOON:
                break;
        }

        return $date_range;
    }
}