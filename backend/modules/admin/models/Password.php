<?php

namespace backend\modules\admin\models;

use Yii;
use yii\base\Model;

class Password extends Model
{
    public $password;
    public $re_password;
    public $auth_key;

    public function rules()
    {
        return [
            ['re_password', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароль не подтвержден'],
            [['password', 're_password'], 'string', 'min' => 6],
            [['password', 're_password', 'auth_key'], 'string', 'max' => 255],
            [['password', 're_password'], 'trim'],
            [['password', 're_password'], 'required'],
        ];
    }

    public function changePassword($id)
    {
        if ($this->validate()) {
            $admin = AuthAdmin::findOne($id);
            $admin->password = Yii::$app->security->generatePasswordHash($this->password);
            $admin->auth_key = Yii::$app->security->generateRandomString();
            $admin->save(false);
            return $admin;
        }
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль',
            're_password' => 'Повторить пароль',
        ];
    }
}
