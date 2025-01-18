<?php

namespace backend\modules\bot\viber;

use backend\modules\bot\models\BotLogger;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\customer\models\ClickStatistic;
use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Delivery;
use backend\modules\shop\models\Order;
use Exception;
use src\helpers\Date;
use Yii;

class VOrder extends VCommon
{
    public function delivery()
    {
        if (Yii::$app->vb->customer->status != Customer::STATUS_ACTIVE) {
            return Yii::$app->vb->action('VRegistration_firstName');
        }
        return $this->keyboard($this->text('choiceOfDelivery'), $this->keyboardDelivery());
    }

    public function keyboardDelivery()
    {
        $delivery = Delivery::find()->asArray()->indexBy('slug')->all();
        $keyboard[] = $this->buttonUrlImgText($delivery[Delivery::DELIVERY_NP]['name'], '/img/vb/empty-min.jpg', Yii::$app->params['homeUrl'] . '/order/np-bot/' . $this->order->id);
        $keyboard[] = $this->buttonImgText($delivery[Delivery::COURIER_DELIVERY]['name'], '/img/vb/empty-min.jpg', ['action' => 'VOrder_courierDelivery']);
        $keyboard[] = $this->buttonImgText($delivery[Delivery::PICKUP]['name'], '/img/vb/empty-min.jpg', ['action' => 'VOrder_pickup']);
        $keyboard[] = $this->buttonImg('back', '/img/vb/nazad-min.jpg', ['action' => 'VCart_menuCart']);
        return $keyboard;
    }

    public function courierDelivery()
    {
        $delivery = Delivery::bySlug(Delivery::COURIER_DELIVERY);
        $this->saveCommand('VOrder_courierDeliverySave');
        return $this->keyboard($delivery->description, $this->keyboardDelivery(), 'regular');
    }

    public function courierDeliverySave()
    {
        $this->order->address = Yii::$app->vb->data->value;
        $this->order->delivery = Delivery::COURIER_DELIVERY;
        if ($this->order->save()) {
            $this->saveCommandNull();
            return $this->payMenu();
        } else {
            $this->errors(Yii::$app->vb->customer->errors);
            return $this->courierDelivery();
        }
    }

    public function pickup()
    {
        $delivery = Delivery::bySlug(Delivery::PICKUP);
        $this->order->delivery = Delivery::PICKUP;
        $this->order->save();
        $this->saveCommandNull();
        $this->payMenu($delivery->description);
    }

    public function payMenu()
    {
        $this->saveClick(ClickStatistic::delivery($this->order->delivery));
        $this->setCart();
        $keyboard[] = $this->buttonImg('paymentUponReceipt', '/img/vb/pay_poluch-min.jpg', ['action' => 'VOrder_paymentUponReceipt']);
        $keyboard[] = $this->buttonUrlImg('onlinePayment', '/img/vb/pay_online-min.jpg', Yii::$app->params['homeUrl'] . '/interkassa/' . $this->order->id);
        $keyboard[] = $this->buttonImg('changeDelivery', '/img/vb/change-delivery.png', ['action' => 'VOrder_delivery']);
        $keyboard[] = $this->buttonUrlImg('dialOperator', '/img/vb/call-min.jpg', $this->dialOperatorUrl());
        $keyboard[] = $this->buttonMainMenu();
        if ($this->order->delivery == Delivery::DELIVERY_NP) {
            $this->text .= $this->order->np->city . PHP_EOL;
            $this->text .= $this->order->np->branch . PHP_EOL;
        }
        if ($this->order->delivery == Delivery::COURIER_DELIVERY) {
            $this->text .= 'Адрес: ' . $this->order->address . PHP_EOL;
        }
        $this->text .= $this->cart->cartText();
        $this->keyboard($this->text, $keyboard);
    }

    public function paymentUponReceipt()
    {
        $this->saveClick(ClickStatistic::PAYMENT_METHOD_UPON_RECEIPT);
        $this->sendSuccess(Order::PAYMENT_METHOD_UPON_RECEIPT);
    }

    public function onlinePayment()
    {
        $this->saveClick(ClickStatistic::PAYMENT_METHOD_PAY_ONLINE);
        $this->sendSuccess(Order::PAYMENT_METHOD_PAY_ONLINE);
    }


    public function confirmedOrder()
    {
        $orderId = \Yii::$app->vb->data->o_id;
        $statusId = \Yii::$app->vb->data->s_id;
        $order = Order::findOne($orderId);
        $order->status = $statusId;
        if (isset($order)) {
            $textAnswer = null;
            $order->status = $statusId;
            switch ($statusId) {
                case Order::STATUS_CONFIRMED:
                    $textAnswer = $this->text("answerConfirmedMessage");
                    break;
                case Order::STATUS_UNCONFIRMED:
                    $textAnswer = $this->text("answerUnConfirmedMessage");
                    break;

            }
            if (!empty($textAnswer) && $order->save()) {
                $this->mainMenu($textAnswer);
            }
        }
    }

    private function sendSuccess($payment_method)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->setCart();
            if (!$this->cart->cacheSumTotal) {
                throw new Exception('Не установлена корзина');
            }
            $this->order->status = Order::STATUS_NEW_VIBER;
            $this->order->cache_sum_total = $this->cart->cacheSumTotal;
            $this->order->payment_method = $payment_method;
            $this->order->created_at = Date::datetime_now();
            $this->order->save();


            $this->text .= $this->text('thanksForYourOrder') . PHP_EOL;
            $this->text .= 'Заказ №: ' . $this->order->id . PHP_EOL;
            $this->text .= $this->cart->cartText();

            // if chosen C.O.D. type payment with NP delivery
            // comment while not need recalculate C.O.D. payments
//            if ($this->order->isNpUponReceipt()) {
//                $sum = $this->cart->getUponReceiptPrice($this->cart->cacheSumTotal); // set on if need back 2% + 20 UAH (recalculate C.O.D. payments)
//                $this->text .= PHP_EOL . str_replace(['{sumOrder}'], [$sum], $this->text('orderMessageChoiceUponReceipt'));
//            }

            $this->mainMenu($this->text);

            $transaction->commit();
            if ($payment_method == Order::PAYMENT_METHOD_UPON_RECEIPT) {
                $admin = new TAdmin();
                $admin->sendOrderInGroup($this->order, $this->cart);
            }
        } catch (Exception $e) {
            $transaction->rollback();
            BotLogger::save_input($e->getMessage(), __METHOD__);
            exit(__METHOD__);
        }
    }
}
