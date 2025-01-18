<?php

namespace src\email;

use Yii;

class OrderNotification
{
    private $_order;
    private $_cart;

    public function __construct($order, $cart)
    {
        $this->_order = $order;
        $this->_cart = $cart;
        $this->sendAdmin();

        if ($this->_order->customer->email) {
            $this->sendCustomer();
        }
    }

    public function sendAdmin()
    {
        Yii::$app->mailer->compose('order_notification', ['cart' => $this->_cart])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->params['adminEmail']])
            ->setTo(Yii::$app->params['adminEmail'])
            ->setSubject('Вы получили заказ: № ' . $this->_order->id)
            ->send();
    }

    public function sendCustomer()
    {
        Yii::$app->mailer->compose('order_notification', ['cart' => $this->_cart])
            ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
            ->setTo($this->_order->customer->email)
            ->setSubject('Магазин ' . Yii::$app->name . '. ' . 'Ваш заказ: № ' . $this->_order->id)
            ->send();
    }
}
