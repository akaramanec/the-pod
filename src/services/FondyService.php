<?php

namespace src\services;

use backend\modules\bot\models\Bot;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\bot\viber\VAdmin;
use backend\modules\shop\models\Order;
use backend\modules\system\models\Fondy;
use backend\modules\system\models\Setting;
use Yii;
use yii\base\BaseObject;
/**
 * @property $_fondyObject FondyService
 * https://www.youtube.com/watch?v=3BD1ZsU2G1k&t=3s
 */
class FondyService extends BaseObject
{
    public $urlCheckout;
    private $_fondyModel;
    private $_cart;
    private $_platformData;

    public function __construct($config = [])
    {
        $setting = Setting::listValue('fondy');
        \Cloudipsp\Configuration::setMerchantId((int)$setting['merchantId']);
        \Cloudipsp\Configuration::setSecretKey($setting['secretKey']);
        \Cloudipsp\Configuration::setCreditKey($setting['creditKey']);
        \Cloudipsp\Configuration::setApiVersion('2.0');
        \Cloudipsp\Configuration::setRequestType('json');
        parent::__construct($config);
    }

    public function getFondyModel()
    {
        return $this->_fondyModel;
    }

    public function setFondyModel($fondyModel)
    {
        $this->_fondyModel = $fondyModel;
    }

    public function getCart()
    {
        return $this->_cart;
    }

    public function setCart($cart)
    {
        $this->_cart = $cart;
    }

    public function getPlatformData()
    {
        return $this->_platformData;
    }

    public function setPlatformData($platformData)
    {
        $this->_platformData = $platformData;
    }

    public function checkout()
    {
        $data = [
            'currency' => 'UAH',
            'order_desc' => 'Shop ' . Yii::$app->name,
            'amount' => $this->_cart->sumTotal * 100,
            'response_url' => $this->_platformData->responseUrlFondy,
            'server_callback_url' => Yii::$app->params['homeUrl'] . '/fondy/index',
            'lang' => 'ru',
            'sender_email' => $this->_fondyModel->order->customer->email,
            'order_id' => $this->_fondyModel->time_id,
            'lifetime' => 36000
        ];
        
        $url = \Cloudipsp\Checkout::url($data);
        $this->_fondyModel->price = $this->_cart->sumTotal;
        $this->_fondyModel->status_checkout = $url->getData();
        $this->_fondyModel->save();
        $this->urlCheckout = $this->_fondyModel->status_checkout['checkout_url'];
    }

    public function statusSuccess($fondy, $data)
    {
        $fondy->status = Fondy::STATUS_PAY;
        $fondy->status_callback = $data;
        $fondy->save();
        $fondy->order->payment_method = Order::PAYMENT_METHOD_PAY_ONLINE;
        $fondy->order->save();
        Fondy::deleteAll(['order_id' => $fondy->order->id, 'status' => Fondy::STATUS_NO_PAY]);
        if ($fondy->order->customer->bot->platform == Bot::TELEGRAM) {
            Yii::$app->tm->customer = $fondy->order->customer;
            Yii::$app->tm->platformId = $fondy->order->customer->platform_id;
            $admin = new TAdmin();
            $admin->orderOnlinePayment();
        }
        if ($fondy->order->customer->bot->platform == Bot::VIBER) {
            Yii::$app->vb->customer = $fondy->order->customer;
            Yii::$app->vb->platformId = $fondy->order->customer->platform_id;
            $admin = new VAdmin();
            $admin->orderOnlinePayment();
        }
    }
}
