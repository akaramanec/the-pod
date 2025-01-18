<?php

namespace backend\modules\admin\models;

use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public $email;
    public $password;
    public $rememberMe;

    private $_user;

    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['email', 'password'], 'trim'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app', 'Invalid email or password'));
            }
        }
    }

    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Admin::findByUsername($this->email);
        }
        return $this->_user;
    }

    private function checkingRole()
    {
        if ($this->getUser()->roles) {
            foreach ($this->getUser()->roles as $role) {
                if (in_array($role->item_name, ['admin', 'dev', 'manager'])) {
                    return true;
                }
            }
        }
        return false;
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'password' => Yii::t('app', 'Password'),
            'rememberMe' => Yii::t('app', 'Remember Me'),
        ];
    }
}
