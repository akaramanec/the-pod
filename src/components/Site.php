<?php

namespace src\components;

use backend\modules\shop\models\Order;
use backend\modules\system\models\SitePageL;
use frontend\models\cart\Cart;
use Yii;
use yii\base\BaseObject;
use yii\helpers\Json;

class Site extends BaseObject
{
    public $contact;
    private $_microMarking;

    public function __construct($config = [])
    {
        $this->contact = SitePageL::find()->where(['id' => 6])->limit(1)->one();
        $this->checkSessionOrder();
        parent::__construct($config);
    }

    public function checkSessionOrder()
    {
        if (isset($_SESSION['order_id']) && $order = Order::findOne($_SESSION['order_id'])) {
            if ($order->payment_method == Order::PAYMENT_METHOD_PAY_ONLINE) {
                $cart = new Cart();
                $cart->clearCart();
            }
        }
    }

    public function getAddedCookie()
    {
        return $this->_addedCookie;
    }

    public function setAddedCookie($cookie)
    {
        $this->_addedCookie = $cookie;
    }

    public function getQ()
    {
        return $this->_q;
    }

    public function setQ($q)
    {
        $this->_q = $q;
    }

    public function getMicroMarking()
    {
        if ($this->_microMarking) {
            $str = '<script type="application/ld+json">';
            $str .= Json::encode($this->_microMarking);
            $str .= '</script>';
            return $str;
        }
    }

    public function setMicroMarking($microMarking)
    {
        $this->_microMarking = $microMarking;
    }

    public function linkTm()
    {
        return Yii::$app->params['chatTm'] . '?start=site';
    }

    public function linkVb()
    {
        return Yii::$app->params['homeUrl'] . '/link-bot/site';
    }

}
