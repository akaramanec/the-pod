<?php

namespace backend\widgets;

use backend\modules\admin\models\Password;
use yii\base\Widget;

class ChangePassword extends Widget
{
    public $id;

    public function run()
    {
        $password = new Password();
        return $this->render('change_password', [
            'password' => $password,
            'id' => $this->id
        ]);
    }
}
