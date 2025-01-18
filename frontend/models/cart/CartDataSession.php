<?php

namespace frontend\models\cart;

use backend\modules\shop\models\ProductMod;

class CartDataSession implements CartInterface
{
    public $items;
    public $discountPercent = 0;

    public function __construct()
    {
        $this->setItems();
    }

    public function setItems()
    {
        if (isset($_SESSION['cart'])) {
            $this->items = ProductMod::find()
                ->where(['id' => array_keys($_SESSION['cart'])])
                ->with(['product'])
                ->all();
        }
    }

    public function qtyItem($id)
    {
        if (isset($_SESSION['cart'][$id]['qty'])) {
            return $_SESSION['cart'][$id]['qty'];
        }
        return 1;
    }

    public function setDiscountPercent()
    {
        return $this->discountPercent = 0;
    }
}
