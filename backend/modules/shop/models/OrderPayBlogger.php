<?php

namespace backend\modules\shop\models;

use Yii;

/**
 * @property int $order_id
 * @property float $sum
 * @property string $created_at
 */
class OrderPayBlogger extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'shop_order_pay_blogger';
    }

    public function rules()
    {
        return [
            [['order_id', 'sum', 'created_at'], 'required'],
            [['order_id'], 'integer'],
            [['sum'], 'number'],
            [['created_at'], 'safe'],
            [['order_id'], 'unique'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    public static function primaryKey()
    {
        return ['order_id'];
    }

    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'sum' => 'Sum',
            'created_at' => 'Created At',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

}
