<?php

namespace backend\modules\customer\models;

use src\behavior\Timestamp;
use Yii;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $message
 * @property string $created_at
 * @property int $status
 */
class CustomerMessage extends \yii\db\ActiveRecord
{
    const STATUS_NOT_VIEWED = 1;
    const STATUS_VIEW = 3;

    public static function tableName()
    {
        return 'bot_customer_message';
    }

    public function behaviors()
    {
        return [
            [
                'class' => Timestamp::className(),
                'create' => ['created_at'],
                'update' => [],
            ],
        ];
    }

    public function rules()
    {
        return [
            [['customer_id', 'message', 'status'], 'required'],
            [['customer_id', 'status'], 'integer'],
            [['message'], 'string'],
            [['created_at'], 'safe'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Пользователь',
            'message' => 'Сообщение',
            'created_at' => 'Отправлено',
            'status' => 'Статус',
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    public static function checkNew()
    {
        return self::find()->where(['status' => self::STATUS_NOT_VIEWED])->orderBy('created_at desc')->all();
    }

    public static function status($status)
    {
        switch ($status) {
            case self::STATUS_VIEW:
                $s = '<div class="badge badge-success text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
            case self::STATUS_NOT_VIEWED:
                $s = '<div class="badge badge-danger text-wrap">' . self::statusesAll()[$status] . '</div>';
                break;
        }
        return $s;
    }

    public static function statusesAll()
    {
        return [
            self::STATUS_NOT_VIEWED => 'Не просмотрено',
            self::STATUS_VIEW => 'Просмотрено',
        ];
    }
}
