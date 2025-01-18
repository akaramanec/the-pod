<?php

namespace backend\modules\shop\models;

use Yii;

/**
 * @property int $move_id
 * @property int $notice_id
 */
class NoticeMoveLink extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'shop_notice_order_link';
    }

    public function rules()
    {
        return [
            [['order_id', 'notice_id'], 'required'],
            [['order_id', 'notice_id'], 'integer'],
            [['order_id', 'notice_id'], 'unique', 'targetAttribute' => ['order_id', 'notice_id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['notice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Notice::className(), 'targetAttribute' => ['notice_id' => 'id']],
        ];
    }

    public static function primaryKey()
    {
        return ['order_id', 'notice_id'];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getNotice()
    {
        return $this->hasOne(Notice::className(), ['id' => 'notice_id']);
    }
}
