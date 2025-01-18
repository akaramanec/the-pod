<?php

namespace backend\modules\system\models;

use backend\modules\shop\models\Order;
use src\behavior\Timestamp;
use Yii;

/**
 * @property int $id
 * @property int $order_id
 * @property string|null $status_callback
 * @property int $status
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Interkassa extends \yii\db\ActiveRecord
{
    const STATUS_NO_PAY = 1;
    const STATUS_PAY = 3;

    public static function tableName()
    {
        return 'pay_interkassa';
    }

    public function behaviors()
    {
        return [
            [
                'class' => Timestamp::className()
            ]
        ];
    }

    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['status'], 'default', 'value' => self::STATUS_NO_PAY],
            [['order_id', 'status'], 'integer'],
            [['status_callback', 'created_at', 'updated_at'], 'safe'],
            [['order_id'], 'unique'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    public static function primaryKey()
    {
        return ['order_id'];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public static function setModel($order_id)
    {
        if ($order_id) {
            $order = new self();
            $order->order_id = $order_id;
            $order->save();
            return $order;
        }
    }
}
