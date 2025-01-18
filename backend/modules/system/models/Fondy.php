<?php

namespace backend\modules\system\models;

use backend\modules\shop\models\Order;
use Exception;
use src\behavior\Timestamp;
use src\helpers\Date;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * @property int $order_id
 * @property string|null $description
 * @property string|null $status_callback
 * @property string|null $status_checkout
 * @property int $status
 * @property int $price
 * @property string $created_at
 */
class Fondy extends \yii\db\ActiveRecord
{
    const STATUS_NO_PAY = 1;
    const STATUS_PAY = 3;

    public static function tableName()
    {
        return 'pay_fondy';
    }

    public function rules()
    {
        return [
            [['time_id', 'order_id', 'price'], 'required'],
            [['order_id', 'status', 'price'], 'integer'],
            [['description'], 'string'],
            [['status'], 'default', 'value' => self::STATUS_NO_PAY],
            [['status_callback', 'status_checkout', 'created_at'], 'safe'],
            [['time_id'], 'string', 'max' => 20],
            [['time_id'], 'unique'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'description' => 'Description',
            'status_callback' => 'Status Callback',
            'status_checkout' => 'Status Checkout',
            'status' => 'Status',
            'price' => 'Price',
            'created_at' => 'Created At',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public static function setModel($order_id)
    {
        if ($order_id) {
            foreach (self::find()->where(['order_id' => $order_id])->all() as $item) {
                if ($item->status == self::STATUS_PAY) {
                    throw new Exception('Заказ оплачен');
                }
            }
            $order = new self();
            $order->order_id = $order_id;
            $order->price = 0.00;
            $order->created_at = Date::datetime_now();
            $order->time_id = $order->order_id . '-' . strtotime($order->created_at);
            $order->save();
            return $order;
        }
    }

}
