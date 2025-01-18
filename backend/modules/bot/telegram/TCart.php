<?php
/**
 * @var $move \backend\modules\rent\models\Move
 * @var $cart \backend\modules\rent\models\cart\Cart
 * @var $session \backend\modules\bot\models\BotSession
 */

namespace backend\modules\bot\telegram;

use backend\modules\rent\models\MoveItemSet;
use backend\modules\rent\models\Product;
use backend\modules\shop\models\OrderItem;
use backend\modules\shop\models\ProductMod;
use Yii;

class TCart extends TCommon
{

    /* Только для кнопки меню. Вместо  метода menuCart */
    public function clickKeyboardCart(): bool
    {
        $this->session->del('selectedProduct');
        $button = $this->setCartMenu();
        $this->button($this->text, $button);
        $this->saveSessionMessageId('mainMessageId');
        return true;
    }

    public function menuCart()
    {
//        $messageId = $this->session->messageId('mainMessageId');
        $messageId = $this->session->getSessionMessageId('productMessageId');
        $this->deleteMessageByMessageId($messageId);
        $button = $this->setCartMenu();
//        if ($messageId) {
//            $this->edit($this->text, $button, $messageId);
//        } else {
//            $this->button($this->text, $button);
//            $this->saveSessionMessageId('mainMessageId');
//        }
        $this->button($this->text, $button);
        $this->saveSessionMessageId('productMessageId');
    }

    protected function setCartMenu(): array
    {
        $this->setCart();
        if ($this->cart->items) {
            $this->text .= $this->cart->cartTextTm();
            $button[] = [["text" => $this->text('editProductsButton'), "callback_data" => $this->encode(['action' => '/TCart_listProduct'])]];
            $button[] = [["text" => $this->text('clearCartButton'), "callback_data" => $this->encode(['action' => '/TCart_clear'])]];
            $button[] = [["text" => $this->text('arrangeButton'), "callback_data" => $this->encode(['action' => '/TOrder_delivery'])]];
        } else {
            $this->text .= $this->text('cartEmpty');
            $button[] = [["text" => $this->text('goToCatalogText'), "callback_data" => $this->encode(['action' => '/TCatalog_clickKeyboardQuickOrder'])]];
        }
        $this->delCommon();
        return $button;
    }

    public function listProduct()
    {
        $this->setCart();
//        $messageId = $this->session->messageId('mainMessageId');
        $messageId = $this->session->getSessionMessageId('productMessageId');
        $this->deleteMessageByMessageId($messageId);
        if ($this->cart->items) {
            $this->setMainMessageId();
            foreach ($this->cart->items as $item) {
                $button[][] = ['text' => '☑️ ' . $item['productName'], 'callback_data' => json_encode(['action' => '/TCart_update', 'mod_id' => $item['modId'], 'order_id' => $this->order->id])];
            }
            $button[] = [["text" => $this->text('arrangeButton'), "callback_data" => $this->encode(['action' => '/TOrder_delivery'])]];
//            return $this->edit($this->cart->cartTextTm(), $button, $messageId);
            $this->button($this->cart->cartTextTm(), $button);
            $this->saveSessionMessageId('productMessageId');
        } else {
            return $this->menuCart($this->text('cartEmpty'));
        }
    }

    public function update()
    {
//        $messageId = $this->session->messageId('mainMessageId');
        $messageId = $this->session->getSessionMessageId('productMessageId');
        $this->deleteMessageByMessageId($messageId);
        $this->setCart();
        $this->setOrderItem();
        $productData = ProductMod::productData($this->_orderItem->mod);
        $this->setTextProduct($productData);
//        return $this->edit($this->text, $this->keyboardProductCart(), $messageId);
        $this->button($this->text, $this->keyboardProductCart());
        $this->saveSessionMessageId('productMessageId');
    }

    public function clear()
    {
//        $messageId = $this->session->messageId('mainMessageId');
        $messageId = $this->session->messageId('productMessageId');
        $this->deleteMessageByMessageId($messageId);
        $this->delCommon();
        $this->session->del('selectedProduct');
        $this->setCart();
        $this->order->clearItems();

        $this->text .= $this->text('cartEmpty');
        $button[] = [["text" => $this->text('goToCatalogText'), "callback_data" => $this->encode(['action' => '/TCatalog_clickKeyboardQuickOrder'])]];

//        if ($messageId) {
//            $this->edit($this->text, $button, $messageId);
//        } else {
//            $this->button($this->text, $button);
//            return $this->saveSessionMessageId('mainMessageId');
//        }
        $this->button($this->text, $button);
        $this->saveSessionMessageId('productMessageId');

    }

    private function keyboardProductCart()
    {
        $button[] = [["text" => $this->infoProductQtyPrice(), "callback_data" => 'none']];
        $button[] = [
            ["text" => 'x', "callback_data" => $this->encode(['action' => '/TCart_delete', 'mod_id' => $this->_orderItem->mod_id, 'order_id' => $this->_orderItem->order_id])],
            ["text" => '-', "callback_data" => $this->encode(['action' => '/TCart_minus', 'mod_id' => $this->_orderItem->mod_id, 'order_id' => $this->_orderItem->order_id])],
            ["text" => '+', "callback_data" => $this->encode(['action' => '/TCart_plus', 'mod_id' => $this->_orderItem->mod_id, 'order_id' => $this->_orderItem->order_id])]
        ];
        $button[] = [["text" => $this->text('backToFoundProducts'), "callback_data" => $this->encode(['action' => '/TCart_listProduct'])]];
        return $button;
    }

    public function add()
    {
        parent::add();
        $this->update();
    }

    public function plus()
    {
        parent::plus();
        $this->update();
    }

    public function minus()
    {
        parent::minus();
        $this->update();
    }

    public function delete()
    {
        parent::delete();
        $messageId = $this->session->get('selectedProduct');
        $this->deleteMessageByMessageId($messageId);
        $this->session->del('selectedProduct');
        return $this->listProduct();
    }
}
