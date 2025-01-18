<?php

namespace src\email;


use Yii;

class NewsletterNotification
{

    public function __construct($newsletter, $customer)
    {
        if ($customer->email) {
            Yii::$app->mailer->compose('newsletter', ['newsletter' => $newsletter, 'customer' => $customer])
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($customer->email)
                ->setSubject('Рассылка от ' . Yii::$app->params['adminEmail'])
                ->send();
        }
    }


}
