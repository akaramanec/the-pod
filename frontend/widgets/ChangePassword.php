<?php


namespace frontend\widgets;

use backend\modules\admin\models\Password;
use Yii;
use yii\base\Widget;

class ChangePassword extends Widget
{

    public function run()
    {
        $password = new Password();
        if ($password->load(Yii::$app->request->post()) && $password->changePassword($password->id)) {
            Yii::$app->session->setFlash('success', 'Войдите в систему');
            Yii::$app->controller->redirect(['/auth/logout']);
        }
        return $this->render('change_password', [
            'password' => $password,
        ]);
    }
}
