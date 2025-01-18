<?php

namespace blog\modules\admin\models;

use backend\modules\customer\models\Customer;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class AuthBlogger extends ActiveRecord implements IdentityInterface
{

    public static function tableName()
    {
        return 'bot_customer_blog';
    }

    public static function findIdentity($id)
    {
        return static::find()
            ->alias('blog')
            ->joinWith(['customer AS customer'])
            ->where(['blog.customer_id' => $id])
            ->andWhere(['customer.blogger' => Customer::BLOGGER_TRUE])
            ->limit(1)
            ->one();
    }

    public static function findByUsername($username)
    {
        return static::find()
            ->alias('blog')
            ->joinWith(['customer AS customer'])
            ->where(['blog.username' => $username])
            ->andWhere(['customer.blogger' => Customer::BLOGGER_TRUE])
            ->limit(1)
            ->one();
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    public function getAuthKey()
    {
//        return $this->auth_key;
    }

    public function generateAuthKey()
    {
//        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

}
