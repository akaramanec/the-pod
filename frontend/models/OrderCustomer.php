<?php

namespace frontend\models;

use backend\modules\shop\models\Order;
use src\behavior\CapitalLetters;
use src\behavior\PhoneFormat;
use src\validators\CheckSpace;
use src\validators\OnlyRussianLetters;
use src\validators\Phone;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shop_order_customer".
 * @property-read mixed $order
 * @property int $id
 * @property int $order_id
 * @property string $first_name
 * @property int|null $last_name
 * @property int $phone
 * @property int|null $email
 * @property int $discount [int(11)]
 */
class OrderCustomer extends ActiveRecord
{
    public $showPhone;

    public static function tableName()
    {
        return 'shop_order_customer';
    }

    public function behaviors()
    {
        return [
            [
                'class' => CapitalLetters::class,
                'fields' => ['first_name', 'last_name'],
            ],
        ];
    }

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'phone', 'email'], 'trim'],
            [['phone'], Phone::class],
//            ['phone', 'filter', 'filter' => function ($value) {
//                $pattern_int = '/[^0-9]/';
//                return preg_replace($pattern_int, '', $value);
//            }],
            [['first_name', 'last_name'], CheckSpace::class],
            [['order_id', 'first_name', 'last_name', 'phone'], 'required'],
            [['order_id'], 'integer'],
            ['email', 'email'],
            ['email', 'filter', 'filter' => function ($value) {
                return mb_strtolower($value, 'UTF-8');
            }],
            [['first_name', 'last_name'], OnlyRussianLetters::class],
            [['first_name', 'last_name', 'email'], 'string', 'max' => 255],
            [['discount'], 'integer', 'min' => 0, 'max' => 100],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'phone' => 'Тел.',
            'email' => 'Email',
            'discount' => 'Скидка %',
        ];
    }

    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function afterFind()
    {
        $this->showPhone = $this->phone ? '+' . $this->phone : '';
    }
}
