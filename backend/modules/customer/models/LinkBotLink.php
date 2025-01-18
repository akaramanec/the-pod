<?php

namespace backend\modules\customer\models;

use Yii;

/**
 * @property int $link_bot_id
 * @property int $customer_id
 */
class LinkBotLink extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'pod_link_bot_link';
    }

    public function rules()
    {
        return [
            [['link_bot_id', 'customer_id'], 'required'],
            [['link_bot_id', 'customer_id'], 'integer'],
            [['link_bot_id', 'customer_id'], 'unique', 'targetAttribute' => ['link_bot_id', 'customer_id']],
            [['link_bot_id'], 'exist', 'skipOnError' => true, 'targetClass' => LinkBot::className(), 'targetAttribute' => ['link_bot_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'link_bot_id' => 'Link Bot ID',
            'customer_id' => 'Customer ID',
        ];
    }

    public function getLinkBot()
    {
        return $this->hasOne(LinkBot::className(), ['id' => 'link_bot_id']);
    }


    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }
}
