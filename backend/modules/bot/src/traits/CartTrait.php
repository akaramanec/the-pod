<?php

namespace backend\modules\bot\src\traits;

use backend\modules\shop\models\OrderItem;
use Yii;

trait CartTrait
{
    public function add()
    {
        $this->setOrderItem();
        if ($this->_orderItem === null) {
            $this->setNewOrderItem(1);
        } else {
            $this->_orderItem->qty += 1;
            $this->_orderItem->save();
        }
    }

    public function qtyMany($qty)
    {
        $this->setOrderItem();
        if ($this->_orderItem === null) {
            $this->setNewOrderItem($qty);
        } else {
            $this->_orderItem->qty = $qty;
            $this->_orderItem->save();
        }
    }

    private function setNewOrderItem($qty)
    {
        $this->_orderItem = new OrderItem();
        $this->_orderItem->order_id = (int)$this->order->id;
        $this->_orderItem->mod_id = (int)$this->mod_id;
        $this->_orderItem->qty = $qty;
        $this->_orderItem->save();
    }

    public function plus()
    {
        $this->setOrderItem();
        $this->_orderItem->qty += 1;
        $this->_orderItem->save();
    }

    public function minus()
    {
        $this->setOrderItem();
        $this->_orderItem->qty -= 1;
        if ($this->_orderItem->qty < 1) {
            $this->_orderItem->qty = 1;
        }
        $this->_orderItem->save();
    }

    public function delete()
    {
        $this->setOrderItem();
        if ($this->_orderItem) {
            $this->_orderItem->delete();
        }
    }


}
