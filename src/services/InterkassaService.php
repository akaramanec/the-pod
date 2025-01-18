<?php

namespace src\services;

use backend\modules\bot\models\Bot;
use backend\modules\bot\src\ApiProduct;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\bot\viber\VAdmin;
use backend\modules\shop\models\Order;
use backend\modules\system\models\Interkassa;
use backend\modules\system\models\Setting;
use frontend\models\cart\Cart;
use frontend\models\cart\CartData;
use src\email\OrderNotification;
use Yii;
use yii\base\BaseObject;

/**
 * @property $_model Interkassa
 */
class InterkassaService extends BaseObject
{
    private $_model;
    private $_cart;
    private $_platformData;

    public function __construct($config = [])
    {
        $setting = Setting::listValue('interkassa');
        parent::__construct($config);
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function setModel($model)
    {
        $this->_model = $model;
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


    public function statusSuccess($data)
    {
        if (isset($data['ik_inv_st']) && $data['ik_inv_st'] == 'success') {
            $this->_model->order->payment_method = Order::PAYMENT_METHOD_PAY_ONLINE;
            $this->_model->order->save();

            $this->_model->status = Interkassa::STATUS_PAY;
            $this->_model->status_callback = $data;
            $this->_model->save();

            $cart = new Cart();
            $cart->build(new CartData($this->_model->order));
            try {
                new OrderNotification($this->_model->order, $cart);
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
            }

            $admin = new TAdmin();
            $admin->sendOrderInGroup($this->_model->order, $cart);

            if (isset($this->_model->order->customer->bot->platform)) {
                if ($this->_model->order->customer->bot->platform == Bot::TELEGRAM) {
                    Yii::$app->tm->customer = $this->_model->order->customer;
                    Yii::$app->tm->platformId = $this->_model->order->customer->platform_id;
                    $admin->orderOnlinePayment();
                }
                if ($this->_model->order->customer->bot->platform == Bot::VIBER) {
                    Yii::$app->vb->customer = $this->_model->order->customer;
                    Yii::$app->vb->platformId = $this->_model->order->customer->platform_id;
                    $admin = new VAdmin();
                    $admin->orderOnlinePayment();
                }
            }
            return true;
        }
    }
}
