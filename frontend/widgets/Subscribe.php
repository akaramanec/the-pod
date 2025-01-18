<?php

namespace frontend\widgets;

use backend\modules\admin\models\AuthSubscribe;
use Yii;
use yii\base\Widget;

class Subscribe extends Widget
{
    public function run()
    {
        $model = new AuthSubscribe();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Вы подписаны на рассылку');
            Yii::$app->controller->redirect(Yii::$app->request->referrer);
        }
        return $this->render('subscribe', [
            'model' => $model
        ]);
    }
}
