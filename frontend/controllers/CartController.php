<?php

namespace frontend\controllers;

use frontend\models\cart\Cart;
use frontend\models\cart\CartDataSession;
use Yii;
use yii\web\Controller;

/**
 * @property Cart $_cart
 */
class CartController extends Controller
{
    public $layout = false;
    private $_session;
    private $_qty;
    private $_cart;

    public function init()
    {
        parent::init();
        $this->_session = Yii::$app->session;
        $this->_session->open();
        $this->_cart = new Cart();
        if (Yii::$app->request->get('qty')) {
            $this->_qty = $this->_cart->wholeNumber(Yii::$app->request->get('qty'));
        }
    }

    public function actionAdd($id)
    {
        $this->_cart->delItem($id);
        $this->_cart->addToCart($id, $this->_qty);
        $this->_cart->build(new CartDataSession());
        $this->_cart->setSessionQtyTotal();
        return $this->renderAjax('cart-modal', [
            'cart' => $this->_cart
        ]);
    }

    public function actionQty($id)
    {
        $this->_cart->qty($id, $this->_qty);
        $this->_cart->build(new CartDataSession());
        $this->_cart->setSessionQtyTotal();
        return $this->renderAjax('cart-modal', [
            'cart' => $this->_cart
        ]);
    }

    public function actionShow()
    {
        $this->_cart->build(new CartDataSession());
        $this->_cart->setSessionQtyTotal();
        return $this->renderAjax('cart-modal', [
            'cart' => $this->_cart
        ]);
    }

    public function actionDelItem($id)
    {
        $this->_cart->delItem($id);
        $this->_cart->build(new CartDataSession());
        $this->_cart->setSessionQtyTotal();
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('cart-modal', [
                'cart' => $this->_cart
            ]);
        }
        if (Yii::$app->request->isGet) {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionFastOrder($id, $qty = 1)
    {
        $this->_cart->delItem($id);
        $this->_cart->addToCart($id, $qty);
        return $this->redirect(['/order']);
    }

}

