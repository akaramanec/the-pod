<?php

namespace frontend\controllers;

use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Order;
use backend\modules\system\models\Fondy;
use backend\modules\system\models\SitePage;
use frontend\models\cart\Cart;
use frontend\models\cart\CartData;
use src\helpers\Date;
use src\services\data\PlatformData;
use src\services\FondyService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * @property FondyService $_fondyObject
 */
class SuccessController extends Controller
{
    public $enableCsrfValidation = false;
    private $_fondyObject;

    public function actionIndex()
    {
        $order = $this->findOrder(Yii::$app->request->get('ik_pm_no'));
        $cart = new Cart();
        $cart->clearCart();
        $cart->build(new CartData($order));

        return $this->render('index', [
            'order' => $order,
            'cart' => $cart,
            'page' => SitePage::page('success')
        ]);
    }

    public function findOrder($order_id)
    {
        $id = null;
        $created_at = null;
        $idTime = explode('-', $order_id);
        if (isset($idTime[0])) {
            $id = $idTime[0];
        }
        if (isset($idTime[1])) {
            $created_at = Date::timestampConverter($idTime[1]);
        }
        $order = Order::find()
            ->where(['id' => $id])
            ->andWhere(['created_at' => $created_at])
            ->andWhere(['status' => Order::STATUS_NEW_SITE])
            ->limit(1)->one();
        if ($order) {
            return $order;
        }
        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }

    public function actionPay($id)
    {
        try {
            $order = $this->findOrder($id);
            $this->_fondyObject = Yii::createObject([
                'class' => FondyService::class
            ]);
            $this->_fondyObject->fondyModel = Fondy::setModel($order->id);
            $this->_fondyObject->platformData = new PlatformData(Customer::CUSTOMER_SITE, $order);
            $cart = new Cart();
            $cart->build(new CartData($order));
            $this->_fondyObject->cart = $cart;
            $this->_fondyObject->checkout();

            return $this->redirect($this->_fondyObject->urlCheckout);
        } catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionReturn($id)
    {
        sleep(2);
        return $this->redirect('/success/' . $id);
    }
}

