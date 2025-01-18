<?php

namespace backend\modules\bot\telegram;

use backend\modules\customer\models\Customer;
use backend\modules\customer\models\Newsletter;
use backend\modules\media\models\Img;
use backend\modules\shop\models\Delivery;
use backend\modules\shop\models\NoticeMoveLink;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\Poll;
use frontend\models\cart\Cart;
use src\helpers\Date;
use Yii;

class TAdmin extends TCommon
{

    public function orderPayMenu()
    {
        $order = new TOrder();
        return $order->payMenu();
    }

    public function orderOnlinePayment()
    {
        $order = new TOrder();
        return $order->onlinePayment();
    }

    public function newsletter(Newsletter $newsletter, $video = null)
    {
        if ($newsletter->video) {
            $video = Yii::$app->params['imgUrl'] . '/newsletter/' . $newsletter->id . '/' . $newsletter->video;
        }
        if ($newsletter->img) {
            $img = Yii::$app->params['imgUrl'] . '/newsletter/' . $newsletter->id . '/' . $newsletter->img;
            $this->text .= '<a href="' . $img . '">.</a>';
        }
        if ($newsletter->text) {
            $this->text .= $newsletter->text;
        }
        if ($video) {
            $this->sendVideo($video);
        }
        if ($this->text) {
            return $this->keyboard($this->text, $this->keyboardMainMenu());
        } elseif ($video) {
            return $this->keyboard($newsletter->video, $this->keyboardMainMenu());
        }
        return ['ok' => true];
    }

    public function noticeNp($noticeNp, $customer)
    {
        $this->startSession($customer->id);
        if ($noticeNp->img) {
            $img = Img::fullPathMain(NOTICE_NP, $noticeNp, '400x400');
            $this->text .= '<a href="' . $img . '">.</a>';
        }
        $this->text .= $noticeNp->text;
        $messageId = $this->session->messageId('mainMessageId');
        $this->deleteMessageByMessageId($messageId);
        $this->sendMessage($this->text);
        $this->startContinue();
    }

    public function notificationAfterSuccessfulOrder($notice, $order)
    {
        $messageId = $this->session->messageId('mainMessageId');
        $this->deleteMessageByMessageId($messageId);

        $text = $notice->name . PHP_EOL;
        $text .= $notice->text;
        if ($notice->img) {
            $img = Img::fullPathMain(NOTICE, $notice, '400x400');
            $text .= '<a href="' . $img . '">.</a>';
        }
        $this->sendMessage($text);
        $nml = new NoticeMoveLink();
        $nml->order_id = $order->id;
        $nml->notice_id = $notice->id;
        $nml->save();
        $this->startContinue();
    }

    public function pollAfterSuccessfulOrder(Poll $poll): void
    {
        $messageId = $this->session->messageId('mainMessageId');
        $this->deleteMessageByMessageId($messageId);
        $text = $poll->question;
        $this->sendMessage($text);
    }

    public function sendTtn($url)
    {
        $messageId = $this->session->messageId('mainMessageId');
        $this->deleteMessageByMessageId($messageId);

        $button[] = [["text" => $this->text('sendTtnButton'), "url" => $url]];
        $this->button($this->text('sendTtn'), $button);
        $this->startContinue();
    }

    public function sendOrderInGroup(Order $order, Cart $cart)
    {
        Yii::$app->tm->platformId = Yii::$app->params['groupTmChatId'];
        $text = 'Заказ № ' . $order->id . PHP_EOL;
        $text .= 'ФИО: ' . Customer::fullName($order->customer) . PHP_EOL;
        $text .= 'Дата: ' . Date::format_datetime($order->created_at) . PHP_EOL;
        $text .= 'Тел: ' . Customer::fullPhoneFormat($order->customer->phone) . PHP_EOL;
        $text .= 'Доставка: ' . Delivery::listSlugName()[$order->delivery] . PHP_EOL;
        $text .= 'Оплата: ' . Order::statusesPaymentMethod()[$order->payment_method] . PHP_EOL;
        $text .= $cart->cartTextTm();
        $this->sendMessage($text);
    }
}
