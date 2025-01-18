<?php

namespace frontend\models\cart;

use backend\modules\bot\src\traits\CartTrait;
use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderItem;
use backend\modules\shop\models\OrderNp;
use Yii;

class CartSite
{
    public $mod_id;
    public $session;
    public $order;
    public $cart;

    use CartTrait;

    public function __construct()
    {
        $this->session = Yii::$app->session;
        $this->session->open();
        $this->setOrder();
    }

    public function setOrderItem()
    {
        $this->_orderItem = OrderItem::findOne(['mod_id' => $this->mod_id, 'order_id' => $this->order->id]);
    }

    public function setCart()
    {
        $this->cart = new Cart();
        $this->cart->build(new CartData($this->order));
    }

    private function setOrder()
    {
        if (isset($_SESSION['order_id']) && $_SESSION['order_id']) {
            $this->order = Order::find()
                ->where(['customer_id' => Customer::CUSTOMER_SITE])
                ->andWhere(['status' => Order::STATUS_NEW])
                ->limit(1)->one();
        }
        if ($this->order == null) {
            $this->order = Order::create(Customer::CUSTOMER_SITE);
            OrderNp::setNew($this->order->id);
        }
        $_SESSION['order_id'] = $this->order->id;
    }
}
