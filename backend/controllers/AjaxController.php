<?php

namespace backend\controllers;

use src\helpers\Common;
use Yii;

class AjaxController extends \yii\web\Controller
{
    public $layout = false;

    public function actionCookieView($name, $value)
    {
        if ($value == 0) {
            unset(Yii::$app->response->cookies[$name]);
        } else {
            Common::cookieAdd($name, $value);
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionPagination($pagination_name, $pagination_value)
    {
        Common::cookieAdd($pagination_name, $pagination_value);
        return true;
    }


}
