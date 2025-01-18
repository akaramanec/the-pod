<?php

namespace backend\modules\bot\controllers;

use backend\modules\bot\models\BotLogger;
use backend\modules\bot\telegram\TBaseCommon;
use Yii;
use backend\modules\bot\models\search\BotLoggerSearch;
use yii\web\Controller;


class LoggerController extends Controller
{

    public $layout = 'base';

    public function actionIndex()
    {
        $searchModel = new BotLoggerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setPagination(['pageSize' => 20]);
        $bot = new TBaseCommon();
        return $this->render('index', [
            'pages' => $dataProvider->getPagination(),
            'dataProvider' => $dataProvider->getModels(),
            'webHookInfo' => $bot->getWebHookInfo(),
        ]);
    }

    public function actionDeleteAll()
    {
        Yii::$app->loggerDb->createCommand("TRUNCATE TABLE auth_logger")->execute();
        Yii::$app->loggerDb->createCommand("TRUNCATE TABLE bot_logger")->execute();
        return $this->redirect(Yii::$app->request->referrer);
    }
}
