<?php

namespace backend\modules\shop\models;

use src\validators\Is;
use Yii;

/**
 * @property int $order_id
 * @property int $mod_id
 * @property int $qty
 */
class OrderItem extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'shop_order_item';
    }

    public function rules()
    {
        return [
            [['order_id', 'mod_id'], 'required'],
            [['order_id', 'mod_id', 'qty'], 'integer'],
            [['qty'], 'default', 'value' => 1],
            [['order_id', 'mod_id'], 'unique', 'targetAttribute' => ['order_id', 'mod_id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    public static function primaryKey()
    {
        return ['order_id', 'mod_id'];
    }

    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'mod_id' => 'Mod ID',
            'qty' => 'Qty',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getMod()
    {
        return $this->hasOne(ProductMod::class, ['id' => 'mod_id']);
    }

    public static function saveItem($items, $order_id)
    {
        self::deleteAll(['order_id' => $order_id]);
        foreach ($items as $item) {
            $orderItem = new self();
            $orderItem->order_id = $order_id;
            $orderItem->mod_id = $item['modId'];
            $orderItem->qty = $item['qtyItem'];
            $orderItem->save();
            if (!$orderItem->save()) {
                Is::errors($orderItem->errors);
            }
        }
    }
}
