<?php

namespace backend\modules\bot\viber;

use backend\modules\shop\models\ProductMod;
use Viber\Api\Keyboard\Button;
use Yii;

class VCart extends VCommon
{

    public function menuCart()
    {
        $this->setCart();
        if ($this->cart->items) {
            $keyboard[] = $this->buttonImg('editProductsButton', '/img/vb/edit-min.jpg', ['action' => 'VCart_listProduct']);
            $keyboard[] = $this->buttonImg('arrangeButton', '/img/vb/oform-min.jpg', ['action' => 'VOrder_delivery']);
            $keyboard[] = $this->buttonMainMenu();
            $this->keyboard($this->cart->cartText(), $keyboard);
        } else {
            $this->mainMenu($this->text('cartEmpty'));
        }
    }

    public function listProduct()
    {
        $this->setCart();
        if ($this->cart->items) {
            foreach ($this->cart->items as $item) {
                $keyboard[] = (new Button())
                    ->setRows(2)
                    ->setColumns(2)
                    ->setActionType('reply')
                    ->setSilent(true)
                    ->setText($item['productName'])
                    ->setTextOpacity(0)
                    ->setBgMedia($item['img'])
                    ->setActionBody($this->encode(['action' => 'VCart_edit', 'mod_id' => $item['modId'], 'order_id' => $this->order->id]));
                $keyboard[] = $this->buttonImgText($item['productName'], '/img/vb/plashka_3-min.jpg', ['action' => 'VCart_edit', 'mod_id' => $item['modId'], 'order_id' => $this->order->id], 3, 2);
                $keyboard[] = $this->buttonImg('x', '/img/vb/del_1-6_2-min.jpg', [
                    'action' => 'VCart_delete',
                    'mod_id' => $item['modId'],
                    'order_id' => $this->order->id
                ], 1, 2);
            }
            $keyboard[] = $this->buttonImg('cartButton', '/img/vb/korz-min.jpg', ['action' => 'VCart_menuCart']);
            $keyboard[] = $this->buttonImg('Каталог', '/img/vb/katalog-min.jpg', ['action' => 'VCatalog_branchQuickOrder']);
            $keyboard[] = $this->buttonMainMenu();
            $this->keyboard($this->text('products'), $keyboard);
        } else {
            $this->menuCart($this->text('cartEmpty'));
            exit();
        }
    }

    public function edit()
    {
        $this->setCart();
        $this->setOrderItem();
        $productData = ProductMod::productData($this->_orderItem->mod, 'viber');
        $this->setTextProduct($productData);
        $this->sendPicture('.', $productData['img']);
        $this->keyboard($this->text, $this->keyboardProductCart());
    }

    public function add()
    {
        parent::add();
        $this->edit();
    }

    public function plus()
    {
        parent::plus();
        $this->edit();
    }

    public function minus()
    {
        parent::minus();
        $this->edit();
    }

    public function delete()
    {
        parent::delete();
        return $this->listProduct();
    }

    private function keyboardProductCart()
    {
        $keyboard[] = $this->buttonNoneText($this->infoProductQtyPrice(), '/img/vb/empty-min.jpg');
        $keyboard[] = $this->buttonImg('x', '/img/vb/del-min.jpg', ['action' => 'VCart_delete', 'mod_id' => $this->_orderItem->mod_id, 'order_id' => $this->_orderItem->order_id], 2);
        $keyboard[] = $this->buttonImg('-', '/img/vb/minus-min.jpg', ['action' => 'VCart_minus', 'mod_id' => $this->_orderItem->mod_id, 'order_id' => $this->_orderItem->order_id], 2);
        $keyboard[] = $this->buttonImg('+', '/img/vb/pluse-min.jpg', ['action' => 'VCart_plus', 'mod_id' => $this->_orderItem->mod_id, 'order_id' => $this->_orderItem->order_id], 2);
        $keyboard[] = $this->buttonImg('cartButton', '/img/vb/korz-min.jpg', ['action' => 'VCart_menuCart']);
        $keyboard[] = $this->buttonImg('backToFoundProducts', '/img/vb/back-min.jpg', ['action' => 'VCart_listProduct']);
        return $keyboard;
    }
}
