<?php

namespace backend\modules\customer\models;

use Yii;

/**
 * This is the model class for table "bot_customer_group_link".
 *
 * @property int $group_id
 * @property int $customer_id
 *
 * @property BotCustomer[] $botCustomers
 * @property BotCustomer $customer
 * @property BotCustomerGroup $group
 */
class CustomerGroupLink extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_customer_group_link';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'customer_id'], 'required'],
            [['group_id', 'customer_id'], 'integer'],
            [['group_id', 'customer_id'], 'unique', 'targetAttribute' => ['group_id', 'customer_id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => CustomerGroup::className(), 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'group_id' => 'Group ID',
            'customer_id' => 'Customer ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasMany(Customer::className(), ['group_id' => 'group_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(CustomerGroup::className(), ['id' => 'group_id']);
    }
}
