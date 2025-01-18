<?php

namespace src\email;


use Yii;

class BloggerNotification
{

    public function __construct($customerBlog)
    {
        if ($customerBlog->customer->email) {
            Yii::$app->mailer->compose('blogger_notification', ['customerBlog' => $customerBlog])
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($customerBlog->customer->email)
                ->setSubject('Доступ в кабинет блогера. С уважением ' . Yii::$app->name)
                ->send();
        }
    }


}
