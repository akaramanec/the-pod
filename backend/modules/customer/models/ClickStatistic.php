<?php

namespace backend\modules\customer\models;

use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Delivery;
use backend\modules\shop\models\Order;
use src\helpers\Date;
use Yii;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $click
 * @property string $created_at
 */
class ClickStatistic extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'customer_click_statistic';
    }

    public function rules()
    {
        return [
            [['customer_id', 'click', 'created_at'], 'required'],
            [['customer_id', 'click'], 'integer'],
            [['created_at'], 'safe'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public static function saveClick($click, $customer_id)
    {
        return Yii::$app->db->createCommand("INSERT INTO customer_click_statistic (customer_id, click, created_at) VALUES (:customer_id, :click, :created_at)", [
            ':customer_id' => $customer_id,
            ':click' => $click,
            ':created_at' => Date::datetime_now(),
        ])->execute();
    }

    const START = 1;
    const FIRST_NAME = 2;
    const LAST_NAME = 3;
    const EMAIL = 4;
    const PHONE = 5;
    const REGISTRATION_CONFIRMATION = 6;
    const PAYMENT_METHOD_UPON_RECEIPT = 7;
    const PAYMENT_METHOD_PAY_ONLINE = 8;
    const DELIVERY_PICKUP = 9;
    const DELIVERY_COURIER = 10;
    const DELIVERY_NP = 11;
    const PAYMENT_METHOD_PAY_TO_CARD = 12;
    const PAYMENT_METHOD_PAY_BY_BLOGGER_BONUS = 13;

    public static function statusList()
    {
        return [
            self::START => 'Старт',
            self::PHONE => 'Поделиться телефоном',
            self::FIRST_NAME => 'Имя',
            self::LAST_NAME => 'Фамилия',
            self::DELIVERY_NP => 'Доставка в отделение "НП"',
            self::DELIVERY_COURIER => 'Доставка курьером',
            self::DELIVERY_PICKUP => 'Самовывоз',
            self::PAYMENT_METHOD_UPON_RECEIPT => 'При получении',
            self::PAYMENT_METHOD_PAY_ONLINE => 'Оплата online',
            self::PAYMENT_METHOD_PAY_TO_CARD => 'Оплата на карту',
            self::PAYMENT_METHOD_PAY_BY_BLOGGER_BONUS => 'Оплата оплата бонусами',
        ];
    }

    public static function delivery($delivery)
    {
        switch ($delivery) {
            case Delivery::DELIVERY_NP:
                $d = self::DELIVERY_NP;
                break;
            case Delivery::COURIER_DELIVERY:
                $d = self::DELIVERY_COURIER;
                break;
            case Delivery::PICKUP:
                $d = self::DELIVERY_PICKUP;
                break;
            default:
                exit('ClickStatistic delivery');
        }
        return $d;
    }
}
