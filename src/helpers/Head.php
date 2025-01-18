<?php

namespace src\helpers;

use backend\modules\shop\models\Order;

class Head
{
    public $orderCheck = [];
    public $orderCountCheck;

    public function __construct()
    {
        $this->orderCheck = Order::find()
            ->where(['status' => [
                Order::STATUS_NEW_TELEGRAM,
                Order::STATUS_NEW_VIBER,
                Order::STATUS_NEW_SITE
            ]])->andWhere(['payment_method' => [
                Order::PAYMENT_METHOD_UPON_RECEIPT,
                Order::PAYMENT_METHOD_PAY_ONLINE
            ]])->with(['orderCustomer', 'botCustomer'])
            ->orderBy('created_at desc')->all();
        $this->orderCountCheck = $this->count($this->orderCheck);
    }

    public function count($c)
    {
        $q = count($c);
        return $q == 0 ? 0 : $q;
    }

    public function activeNew($check)
    {
        if ($check >= 1) {
            return 'active-check';
        }
    }
}
