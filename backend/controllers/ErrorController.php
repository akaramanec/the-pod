<?php

namespace backend\controllers;

use yii\web\Controller;


class ErrorController extends Controller
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'layout' => 'base'
            ],
        ];
    }

    public function actionCheckRelevance()
    {
        $this->layout = 'login';
        return $this->render('check-relevance');
    }

}
