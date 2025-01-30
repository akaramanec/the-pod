<?php

namespace backend\controllers;

use backend\modules\bot\src\ApiProduct;
use backend\modules\rent\models\cart\Cart;
use backend\modules\rent\models\cart\CartData;
use backend\modules\rent\models\Move;
use common\helpers\DieAndDumpHelper;
use src\helpers\Demo;
use Yii;
use yii\helpers\Json;

class HomeController extends \yii\web\Controller
{

    public $layout = 'base';

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionDemo()
    {
        if (($id = \Yii::$app->request->get('id')) !== NULL) {
            new Demo($id);
        } else {
            DieAndDumpHelper::dd('id is not set');
        }
    }

    public function actionElement()
    {
        return $this->render('element');
    }

    public function actionTest()
    {
        $email = 'akaramanec@gmail.com';
        DieAndDumpHelper::dd(Yii::$app->security->generatePasswordHash($email));
    }
}
