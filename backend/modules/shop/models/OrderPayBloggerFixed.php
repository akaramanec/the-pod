<?php

namespace backend\modules\shop\models;

use backend\modules\customer\models\Customer;
use src\behavior\Timestamp;
use Yii;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $sum
 * @property string|null $data
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class OrderPayBloggerFixed extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'shop_order_pay_blogger_fixed';
    }

    public function behaviors()
    {
        return [
            [
                'class' => Timestamp::class,
            ]
        ];
    }

    public function rules()
    {
        return [
            [['customer_id', 'sum'], 'required'],
            [['customer_id', 'sum'], 'integer'],
            ['sum', 'filter', 'filter' => function ($value) {
                return preg_replace("/[^0-9]/", '', $value);
            }],
            [['data', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'sum' => 'Сумма',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function setData()
    {
        $this->data = [
            'percent_level_1' => $this->customer->blog->percent_level_1,
            'percent_level_2' => $this->customer->blog->percent_level_2
        ];
        $this->save(false);
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }
}
