<?php

namespace frontend\controllers;


use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderNp;
use frontend\models\cart\Cart;
use frontend\models\cart\CartDataSession;
use frontend\models\cart\CartSite;
use Yii;
use yii\web\Controller;

/**
 * @property CartSite $_cartSite
 */
class CartDbController extends Controller
{

    public $layout = false;
    private $_qty;
    private $_cartSite;

    public function init()
    {
        parent::init();
        $this->_session = Yii::$app->session;
        $this->_session->open();
        $this->_cartSite = new CartSite();
    }


    public function actionAdd($id, $qty)
    {
        $this->_cartSite->mod_id = $id;
        $this->_cartSite->add($qty);
        return $this->renderAjax('cart-modal', [
            'cart' => $this->_cartSite->cart
        ]);
    }

    public function actionAdd13($id, $qty)
    {
        $this->_cartSite->mod_id = $id;
        $this->_cartSite->qty($qty);
        return $this->renderAjax('cart-modal', [
            'cart' => $this->_cartSite->cart
        ]);
    }

    public function actionQty($id, $qty)
    {
        $this->_cartSite->mod_id = $id;
        $this->_cartSite->qty($qty);
        return $this->renderAjax('cart-modal', [
            'cart' => $this->_cartSite->cart
        ]);
    }

    public function actionShow()
    {
        $this->_cartSite->setCart();
        return $this->renderAjax('cart-modal', [
            'cart' => $this->_cartSite->cart
        ]);
    }

    public function actionDelItem($id)
    {
        $this->_cartSite->mod_id = $id;
        $this->_cartSite->delete();
        $this->_cartSite->setCart();
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('cart-modal', [
                'cart' => $this->_cartSite->cart
            ]);
        }
        if (Yii::$app->request->isGet) {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }


}

