<?php

namespace backend\modules\customer\models;

use Yii;

/**
 * @property int $customer_id
 * @property int $tag_id
 */
class CustomerTagLink extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'bot_customer_tag_link';
    }

    public function rules()
    {
        return [
            [['customer_id', 'tag_id'], 'required'],
            [['customer_id', 'tag_id'], 'integer'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => CustomerTag::class, 'targetAttribute' => ['tag_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'customer_id' => Yii::t('app', 'Customer ID'),
            'tag_id' => Yii::t('app', 'Tag ID'),
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getTag()
    {
        return $this->hasOne(CustomerTag::class, ['id' => 'tag_id']);
    }

//    public static function setTagNew($customer_id)
//    {

//    }
}
