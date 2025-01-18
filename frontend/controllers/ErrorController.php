<?php

namespace frontend\controllers;

use yii\web\Controller;


class ErrorController extends Controller
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionMessage()
    {
        return $this->render('message');
    }

    public function actionCheckRelevance()
    {
        return $this->render('check-relevance');
    }
}
