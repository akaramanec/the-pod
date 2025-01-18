<?php

namespace backend\modules\bot\models;

use backend\modules\admin\models\AuthAdmin;
use backend\modules\customer\models\Customer;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property int $id
 * @property string $platform
 * @property string $username
 * @property string $first_name
 * @property string $token
 * @property int $status
 */
class Bot extends \yii\db\ActiveRecord
{
    const TELEGRAM = 'telegram';
    const VIBER = 'viber';
    const MESSENGER = 'messenger';

    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 3;

    public static function tableName()
    {
        return 'bot';
    }

    public function rules()
    {
        return [
            [['platform', 'username', 'first_name', 'token', 'status'], 'required'],
            [['status'], 'integer'],
            [['platform'], 'string'],
            [['username', 'first_name'], 'string', 'max' => 255],
            [['username', 'first_name', 'token'], 'trim'],
            [['token'], 'string', 'max' => 100],
            ['token', 'unique', 'message' => Yii::t('app', 'This token is already taken')],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'platform' => 'Платформа',
            'username' => 'Username',
            'first_name' => 'First Name',
            'token' => 'Токен',
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function getCustomer()
    {
        return $this->hasMany(Customer::class, ['bot_id' => 'id']);
    }

    public function getCommand()
    {
        return $this->hasMany(BotCommand::class, ['bot_id' => 'id'])->indexBy('name');
    }

    public static function allName()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'first_name');
    }

    public static function bot($platform)
    {
        return self::find()
            ->where(['platform' => $platform])
            ->andWhere(['status' => Bot::STATUS_ACTIVE])
            ->limit(1)
            ->one();
    }

    public static function allPlatforms()
    {
        return [
            self::TELEGRAM => 'Telegram',
            self::VIBER => 'Viber',
        ];
    }

    public static function statusesAll()
    {
        return [
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_NOT_ACTIVE => 'Не активен',
        ];
    }

    public static function status($status)
    {
        $s = '';
        switch ($status) {
            case self::STATUS_ACTIVE:
                $s = 'Активен';
                break;
            case self::STATUS_NOT_ACTIVE:
                $s = 'Не активен';
                break;
        }
        return $s;
    }

    public static function setDev()
    {
        $tm = self::bot(self::TELEGRAM);
        $tm->username = 'pod_dev_bot';
        $tm->first_name = 'podDev';
        $tm->token = '1191971875:AAHqJtWDExwPlHWSE9i4zsNVmXmWkRlrlp8';
        $tm->save();
        $vb = self::bot(self::VIBER);
        $vb->username = 'poddev';
        $vb->first_name = 'poddev';
        $vb->token = '4bddadc77a67d038-f4f24f759b54f58f-1f61ce452b266205';
        $vb->save();
    }

}
