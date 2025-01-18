<?php

namespace backend\modules\shop\models;

use Yii;

/**
 * @property int $order_id
 * @property string|null $agent
 * @property string|null $demand
 */
class OrderMoysklad extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'shop_order_moysklad';
    }

    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id'], 'integer'],
            [['agent', 'demand'], 'safe'],
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

    public static function getModel($order_id)
    {
        $m = self::findOne($order_id);
        if ($m === null) {
            $m = new self();
            $m->order_id = $order_id;
            $m->save();
        }
        return $m;
    }
}
