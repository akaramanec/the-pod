<?php

namespace frontend\models\cart;


class CartData implements CartInterface
{
    public $order;
    public $items;
    public $discountPercent = 0;

    public function __construct($order)
    {
        $this->order = $order;
        $this->setItems();
        $this->setDiscountPercent();
    }

    public function setItems()
    {
        $this->items = $this->order->mod;
    }

    public function qtyItem($id)
    {
        return $this->order->orderItem[$id]->qty;
    }

    public function setDiscountPercent()
    {
        if (isset($this->order->customer->discount)) {
            return $this->discountPercent = $this->order->customer->discount;
        }
    }
}
