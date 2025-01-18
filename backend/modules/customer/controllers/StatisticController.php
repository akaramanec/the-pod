<?php

namespace backend\modules\customer\controllers;

use backend\controllers\BaseController;
use backend\modules\customer\models\Statistic;
use src\helpers\Date;
use Yii;
use yii\helpers\Json;

class StatisticController extends BaseController
{
    public $layout = 'base';

    public function actionIndex()
    {
        return $this->render('index', [
            'statistic' => new Statistic()
        ]);
    }

    public function actionStatisticDate($date_from = null, $date_to = null)
    {
        try {
            $session = Yii::$app->session;
            $session->open();
            if ($date_from) {
                $_SESSION['date_from'] = Date::date_converter($date_from);
            }
            if ($date_to) {
                $_SESSION['date_to'] = Date::date_converter($date_to);
            }
            return Json::encode(['ok' => true]);
        } catch (\Exception $e) {
            return Json::encode(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    public function actionDeleteStatisticDate()
    {
        unset($_SESSION['date_from']);
        unset($_SESSION['date_to']);
        return $this->redirect(Yii::$app->request->referrer);
    }
}
