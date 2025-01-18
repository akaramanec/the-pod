<?php

namespace backend\controllers;

use src\cookie\Flash;
use src\helpers\Common;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class BaseController extends Controller
{

    protected function requestFromPostOrCookies($nameSearch)
    {

        return ArrayHelper::merge($this->getQuery($nameSearch), [
            'sort' => Yii::$app->request->get('sort'),
            'page' => Yii::$app->request->get('page'),
            'per-page' => Yii::$app->request->get('per-page'),
        ]);
    }

    private function getQuery($nameSearch)
    {
        if (Yii::$app->request->isPost) {
            Common::cookieAdd($nameSearch, [$nameSearch => Yii::$app->request->post($nameSearch)]);
            return [$nameSearch => Yii::$app->request->post($nameSearch)];
        }
        if (Yii::$app->request->isGet) {
            return Yii::$app->request->cookies->getValue($nameSearch);
        }
    }

    public function actionResetFilters($model)
    {
        unset(Yii::$app->response->cookies[$model]);
        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function js($name)
    {
        Yii::$app->view->registerJsFile('/js/' . $name . '.js',
            [
                'depends' => [
                    \yii\web\YiiAsset::class,
                    \yii\bootstrap4\BootstrapPluginAsset::class,
                    \backend\assets\AppAsset::class,
                ]
            ]);
    }

    public function actionDeleteFlash()
    {
        Flash::remove(Yii::$app->request->post('delete_flash'));
        return $this->redirect(Yii::$app->request->referrer);
    }
}
