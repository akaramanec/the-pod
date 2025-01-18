<?php

namespace backend\modules\system\controllers;

use yii\web\Controller;

class SystemController extends Controller
{


    public function actionIndex()
    {
        return $this->render('index');
    }
}
