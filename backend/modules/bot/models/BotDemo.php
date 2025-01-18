<?php

namespace backend\modules\bot\models;

use backend\modules\customer\models\Customer;
use Yii;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $price
 * @property string $comment
 * @property int $status
 */
class BotDemo extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_EDIT = 2;

    public static function tableName()
    {
        return 'bot_demo';
    }

    public function rules()
    {
        return [
            [['customer_id', 'status'], 'required'],
            [['customer_id', 'price', 'status'], 'integer'],
            [['comment'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'price' => 'Price',
            'comment' => 'Comment',
            'status' => 'Status',
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }
}
