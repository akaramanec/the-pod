<?php

namespace src\email;

use Yii;

class NoticeNpNotification
{

    public function __construct($noticeNp, $order)
    {
        if ($order->customer->email) {
            Yii::$app->mailer->compose('notice-np', ['noticeNp' => $noticeNp, 'order' => $order])
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($order->customer->email)
                ->setSubject('Уведомления НП ' . Yii::$app->params['adminEmail'])
                ->send();
        }
    }


}
