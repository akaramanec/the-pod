<?php

namespace backend\modules\bot\viber;

use backend\modules\customer\models\Newsletter;
use backend\modules\media\models\Img;
use backend\modules\shop\models\NoticeMoveLink;
use Yii;

class VAdmin extends VCommon
{
    public function orderPayMenu()
    {
        $order = new VOrder();
        return $order->payMenu();
    }

    public function orderOnlinePayment()
    {
        $order = new VOrder();
        return $order->onlinePayment();
    }

    public function newsletter(Newsletter $newsletter)
    {
        if ($newsletter->img) {
            $img = Img::cache(NEWSLETTER, $newsletter->id, $newsletter->img, '400x400');;
            if (strlen($newsletter->text) < 120) {
                $this->sendPictureKeyboard($newsletter->text, $this->mainMenuKeyboard(), $img);
            } else {
                $this->sendPicture(Yii::$app->name, $img);
                $this->keyboard($newsletter->text, $this->mainMenuKeyboard());
            }
        } else {
            $this->keyboard($newsletter->text, $this->mainMenuKeyboard());
        }
    }

    public function noticeNp($noticeNp, $customer)
    {
        $img = null;
        if ($noticeNp->img) {
            $img = Img::cache(NOTICE_NP, $noticeNp->id, $noticeNp->img, '400x400');
        }
        if ($img) {
            if (strlen($noticeNp->text) < 120) {
                $this->sendPictureKeyboard($noticeNp->text, $this->mainMenuKeyboard(), $img);
            } else {
                $this->sendPicture(Yii::$app->name, $img);
                $this->keyboard($noticeNp->text, $this->mainMenuKeyboard());
            }
        } else {
            $this->keyboard($noticeNp->text, $this->mainMenuKeyboard());
        }
    }

    public function notificationAfterSuccessfulOrder($notice, $order)
    {
        $text = $notice->name . PHP_EOL;
        $text .= $notice->text;
        if ($notice->img) {
            $img = Img::fullPathMain(NOTICE, $notice, '400x400');
            if (strlen($text) < 120) {
                $this->sendPictureKeyboard($text, $this->mainMenuKeyboard(), $img);
            } else {
                $this->sendPicture(Yii::$app->name, $img);
                $this->keyboard($text, $this->mainMenuKeyboard());
            }
        } else {
            $this->keyboard($text, $this->mainMenuKeyboard());
        }
        $nml = new NoticeMoveLink();
        $nml->order_id = $order->id;
        $nml->notice_id = $notice->id;
        $nml->save();
    }

    public function sendTtn($url)
    {
        $text = $this->text('sendTtn') . PHP_EOL;
        $text .= $url;
        $this->keyboard($text, $this->mainMenuKeyboard());
    }
}


