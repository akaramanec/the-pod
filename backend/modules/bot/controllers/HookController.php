<?php

namespace backend\modules\bot\controllers;

use Yii;
use backend\modules\bot\models\BotLogger;
use yii\rest\Controller;

class HookController extends Controller
{
    public function actionTelegram()
    {
//        return true;
        $input = json_decode(file_get_contents('php://input'));
        BotLogger::save_input($input, __METHOD__);
        Yii::$app->tm->input = $input;
        Yii::$app->tm->run();
        exit;
    }

    public function actionViber()
    {
        $input = json_decode(file_get_contents('php://input'));
        BotLogger::save_input($input, __METHOD__);
        Yii::$app->vb->input = $input;
        Yii::$app->vb->run();
        exit;
    }
}
