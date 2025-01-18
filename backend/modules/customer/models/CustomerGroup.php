<?php

namespace backend\modules\customer\models;

use Yii;

/**
 * This is the model class for table "bot_customer_group".
 *
 * @property int $id
 *
 * @property BotCustomer[] $botCustomers
 * @property BotCustomerGroupLink[] $botCustomerGroupLinks
 */
class CustomerGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_customer_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasMany(Customer::className(), ['group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getBotCustomerGroupLinks()
//    {
//        return $this->hasMany(CustomerGroupLink::className(), ['group_id' => 'id']);
//    }
}
