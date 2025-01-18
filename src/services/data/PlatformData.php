<?php

namespace src\services\data;

use backend\modules\bot\models\Bot;
use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Order;
use Yii;

class PlatformData
{
    public $linkBackInBot;
    public $redirectInBot;
    public $redirectInBotOnlinePay;
    public $callbackUrl;
    public $order;

    public function __construct($platform, $order = null)
    {
        $this->order = $order;
        if ($platform == Bot::TELEGRAM) {
            $this->telegram();
        }
        if ($platform == Bot::VIBER) {
            $this->viber();
        }
        if ($platform == Customer::CUSTOMER_SITE) {
            $this->site();
        }
    }

    private function telegram()
    {
        $this->linkBackInBot = Yii::$app->params['chatTm'];
        $this->redirectInBot = Yii::$app->params['chatTm'];
        $this->redirectInBotOnlinePay = Yii::$app->params['chatTm'];
        $this->callbackUrl = Yii::$app->params['homeUrl'] . '/telegram';
    }

    private function viber()
    {
        $this->linkBackInBot = Yii::$app->params['chatVb'] . '&context=action=VCart_menuCart';
        $this->redirectInBot = Yii::$app->params['chatVb'] . '&context=action=VOrder_payMenu';
        $this->redirectInBotOnlinePay = Yii::$app->params['chatVb'];
        $this->callbackUrl = Yii::$app->params['homeUrl'] . '/viber';
    }

    private function site()
    {
        $this->callbackUrl = Yii::$app->params['homeUrl'] . '/success';
    }
}
