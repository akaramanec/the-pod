<?php

namespace backend\modules\bot\telegram;

use backend\modules\bot\models\BotLogger;
use backend\modules\customer\models\ClickStatistic;
use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Delivery;
use backend\modules\shop\models\Order;
use blog\models\CacheBlogger;
use Exception;
use src\helpers\Date;
use Yii;

class TOrder extends TCommon
{

    /** @var Customer $customer */
    protected $customer;

    public function __construct()
    {
        parent::__construct();

        $this->customer = \Yii::$app->tm->customer;
    }

    public function delivery()
    {
        if (Yii::$app->tm->customer->status != Customer::STATUS_ACTIVE) {
            return Yii::$app->tm->action('/TAuth_phone');
        }
        $delivery = Delivery::find()->asArray()->indexBy('slug')->all();
//        $messageId = $this->session->messageId('mainMessageId');
        $messageId = $this->session->messageId('productMessageId');
        $this->deleteMessageByMessageId($messageId);
        $button[] = [["text" => $delivery[Delivery::DELIVERY_NP]['name'], "url" => Yii::$app->params['homeUrl'] . '/order/np-bot/' . $this->order->id]];
        $button[] = [["text" => $delivery[Delivery::COURIER_DELIVERY]['name'], "callback_data" => $this->encode(['action' => '/TOrder_courierDelivery'])]];
        $button[] = [["text" => $delivery[Delivery::PICKUP]['name'], "callback_data" => $this->encode(['action' => '/TOrder_pickup'])]];
        $button[] = [["text" => $this->text('back'), "callback_data" => $this->encode(['action' => '/TCart_menuCart'])]];
//        return $this->edit($this->text('choiceOfDelivery'), $button, $messageId);
        $this->button($this->text('choiceOfDelivery'), $button);
        $this->saveSessionMessageId('productMessageId');
    }

    public function courierDelivery()
    {
        $delivery = Delivery::bySlug(Delivery::COURIER_DELIVERY);
        $this->saveCommand('/TOrder_courierDeliverySave');
        $this->sendMessage($delivery->description);
        return $this->session->saveCommonRequest($this->request);
    }

    public function courierDeliverySave()
    {
        $this->session->saveCommonMessageId(Yii::$app->tm->messageId);
        $this->order->address = Yii::$app->tm->data->value;
        $this->order->delivery = Delivery::COURIER_DELIVERY;
        if ($this->order->save()) {
            $this->saveCommandNull();
            return $this->payMenu();
        } else {
            $this->errors(Yii::$app->tm->customer->errors);
            $this->session->saveCommonRequest($this->request);
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

    //TOrder_payMenu
    public function payMenu($text = null)
    {
        $this->saveClick(ClickStatistic::delivery($this->order->delivery));
        $this->delCommon();
//        $messageId = $this->session->messageId('mainMessageId');
        $messageId = $this->session->messageId('productMessageId');
        $this->deleteMessageByMessageId($messageId);
        $this->setCart();

        if ($this->customer->blogger
            && isset($this->customer->blog->sumDebt)
            && $this->customer->blog->sumDebt
            && empty($this->cart->sumPayByBonus)
        ) {
            $button[] = [["text" => $this->text('paymentByBloggerBonus'), "callback_data" => $this->encode(['action' => '/TOrder_paymentByBonus'])]];
        }

        if (!$this->customer->black_list) {
            $button[] = [["text" => $this->text('paymentUponReceipt'), "callback_data" => $this->encode(['action' => '/TOrder_paymentUponReceipt'])]];
        }
        $button[] = [["text" => $this->text('paymentToCard'), "callback_data" => $this->encode(['action' => '/TOrder_paymentToCard'])]];
        //$button[] = [["text" => $this->text('onlinePayment'), "url" => Yii::$app->params['homeUrl'] . '/interkassa/' . $this->order->id]];
        $button[] = [["text" => $this->text('changeDelivery'), "callback_data" => $this->encode(['action' => '/TOrder_delivery'])]];
        $button[] = [["text" => $this->text('dialOperator'), "url" => $this->dialOperatorUrl()]];
        if ($this->order->delivery == Delivery::DELIVERY_NP) {
            $this->text .= $this->order->np->city . PHP_EOL;
            $this->text .= $this->order->np->branch . PHP_EOL;
        }
        if ($this->order->delivery == Delivery::COURIER_DELIVERY) {
            $this->text .= 'Адрес: ' . $this->order->address . PHP_EOL;
        }
        if ($text) {
            $this->text .= $text . PHP_EOL;
        }
        $this->text .= $this->cart->cartTextTm() . PHP_EOL;
        $this->text .= PHP_EOL . $this->text('choiceOfPayment');

        if ($this->customer->black_list) {
//            $this->editMessageText($this->text("blackList"));
            $this->sendMessage($this->text("blackList"));
            $this->session->saveCommonRequest($this->request);
            $this->button($this->text, $button);
            $this->saveSessionMessageId('productMessageId');
        }

//        return $this->edit($this->text, $button, $messageId);
        $this->button($this->text, $button);
        $this->saveSessionMessageId('productMessageId');
    }

    public function paymentByBonus()
    {
        $this->saveClick(ClickStatistic::PAYMENT_METHOD_PAY_BY_BLOGGER_BONUS);
        $this->delCommon();
//        $messageId = $this->session->messageId('mainMessageId');
        $messageId = $this->session->messageId('productMessageId');
        $this->deleteMessageByMessageId($messageId);

        $this->setCart();
        $button[] = [["text" => $this->text('buttonConfirm'), "callback_data" => $this->encode(['action' => '/TOrder_confirmBonusPay'])]];
        $button[] = [["text" => $this->text('buttonNotConfirm'), "callback_data" => $this->encode(['action' => '/TOrder_notConfirmBonusPay'])]];
        $this->text .= $this->cart->cartTextTm() . PHP_EOL;

        $this->text .= PHP_EOL . "-----------------------------------------------------------------------";
        $bonusPayed = $this->cart->sumTotalDiscount - $this->cart->additionalDiscountSum;
        if ($bonusPayed > $this->customer->blog->sumDebt) {
            $bonusPayed = $this->customer->blog->sumDebt;
        }
        $this->cart->sumPayByBonus = $bonusPayed;
        $sumTotalResult = $this->cart->getOrderPriceWithBloggerBonus();

        $this->text .= PHP_EOL . "Сума к списанию: " . $this->cart->showPrice($bonusPayed);
        $this->text .= PHP_EOL . "К оплате: " . $this->cart->showPrice($sumTotalResult);
        $this->session->set("SumPayOrderByBonus", $bonusPayed);
//        $this->edit($this->text, $button, $messageId);
        $this->button($this->text, $button);
        $this->saveSessionMessageId('productMessageId');
        return true;
//        $this->sendSuccess(Order::PAYMENT_METHOD_UPON_RECEIPT);
    }

    public function confirmBonusPay()
    {
        $this->saveClick(ClickStatistic::PAYMENT_METHOD_PAY_BY_BLOGGER_BONUS);
        $this->delCommon();
        $this->setCart();
        if ($this->cart->sumPayByBonus == ($this->cart->sumTotalDiscount - $this->cart->additionalDiscountSum)) {
            $this->sendSuccess(Order::PAYMENT_METHOD_PAY_BY_BLOGGER_BONUS);
        } else {
            $this->payMenu();
        }
    }

    public function notConfirmBonusPay()
    {
        $this->session->del("SumPayOrderByBonus");
        $this->payMenu();
    }

    public function paymentUponReceipt()
    {
        $this->saveClick(ClickStatistic::PAYMENT_METHOD_UPON_RECEIPT);
        $this->sendSuccess(Order::PAYMENT_METHOD_UPON_RECEIPT);
    }

    public function paymentToCard()
    {
        $this->saveClick(ClickStatistic::PAYMENT_METHOD_PAY_TO_CARD);
        $this->sendSuccess(Order::PAYMENT_METHOD_PAY_TO_CARD);
    }

    public function onlinePayment()
    {
        $this->saveClick(ClickStatistic::PAYMENT_METHOD_PAY_ONLINE);
        $this->sendSuccess(Order::PAYMENT_METHOD_PAY_ONLINE);
    }

    public function confirmedOrder()
    {
        $orderId = \Yii::$app->tm->data->o_id;
        $statusId = \Yii::$app->tm->data->s_id;
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
                $this->deleteMessageByMessageId(Yii::$app->tm->messageId);
                $this->mainMenu($textAnswer);
            }
        }
    }

    private function sendSuccess($payment_method)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->setCart();
            $this->session->del("SumPayOrderByBonus");
            if (!$this->cart->cacheSumTotal) {
                throw new Exception('Не установлена корзина');
            }
            $this->order->status = Order::STATUS_NEW_TELEGRAM;
            $this->order->cache_sum_total = $this->cart->cacheSumTotal;
            $this->order->blogger_bonus = $this->cart->sumPayByBonus;
            $this->order->payment_method = $payment_method;
            $this->order->created_at = Date::datetime_now();
            $this->order->save();

            $this->text .= $this->text('thanksForYourOrderTm') . PHP_EOL;
            $this->text .= 'Заказ №: ' . $this->order->id . PHP_EOL;
            $this->text .= $this->cart->cartTextTm();

            if (!empty($this->cart->sumPayByBonus)) {
                $this->customer->blog->sumDebt -= $this->cart->sumPayByBonus;
                $this->customer->blog->save();
                $cacheBlogger = new CacheBlogger();
                $cacheBlogger->customerBlogger = $this->customer;
                $cacheBlogger->blog = $this->customer->blog;
                $cacheBlogger->setCache($cacheBlogger->customerBlogger);
            }

            $this->session->del('selectedProduct');

            // if chosen C.O.D. type payment with NP delivery
            // comment while not need recalculate C.O.D. payments
//            if ($this->order->isNpUponReceipt()) {
//                $sum = $this->cart->getUponReceiptPrice($this->cart->cacheSumTotal);
//                $this->text .= PHP_EOL . str_replace(['{sumOrder}'], [$sum], $this->text('orderMessageChoiceUponReceipt'));
//            }

//            $this->setMainMessageId();
//            $this->editMessageText($this->text);
            $messageId = $this->session->messageId('productMessageId');
            $this->deleteMessageByMessageId($messageId);
//            $this->editMessageText($this->text);
            $this->sendMessage($this->text);
            $this->session->saveCommonRequest($this->request);
            $transaction->commit();
            if ($payment_method == Order::PAYMENT_METHOD_PAY_TO_CARD) {
                $this->sendMessage($this->text('continuePaymentCard'));
            }
            if (in_array($payment_method, [Order::PAYMENT_METHOD_UPON_RECEIPT, Order::PAYMENT_METHOD_PAY_TO_CARD])) {
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
