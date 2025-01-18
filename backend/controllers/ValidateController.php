<?php

namespace backend\controllers;

use backend\modules\admin\models\Password;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class ValidateController extends Controller
{

    public function actionPassword()
    {
        return $this->validateModel(new Password());
    }

    public function actionCommon($model)
    {
        $model = new $model();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    private function validateModel($model)
    {
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }
}
