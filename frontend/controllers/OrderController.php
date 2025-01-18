<?php

namespace frontend\controllers;

use backend\modules\bot\models\Bot;
use backend\modules\bot\src\ApiProduct;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\bot\viber\VAdmin;
use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Delivery;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderItem;
use backend\modules\shop\models\OrderNp;
use backend\modules\shop\models\OrderTracking;
use backend\modules\system\models\SitePage;
use common\helpers\DieAndDumpHelper;
use frontend\models\cart\Cart;
use frontend\models\cart\CartData;
use frontend\models\cart\CartDataSession;
use frontend\models\OrderCustomer;
use src\email\OrderNotification;
use src\services\data\PlatformData;
use src\services\np\Search;
use src\validators\Is;
use Yii;
use yii\web\Controller;

/**
 * @property Cart $_cart
 * @property Order $_order
 * @property OrderNp $_orderNp
 * @property OrderCustomer $_customer
 */
class OrderController extends Controller
{
    private $_cart;
    private $_order;
    private $_orderNp;
    private $_customer;

    public function init()
    {
        parent::init();
        $session = Yii::$app->session;
        $session->open();
    }

    public function actionIndex()
    {
        $this->_cart = new Cart();
        $this->_cart->build(new CartDataSession());
        $this->setOrder();
        if ($this->_order->load(Yii::$app->request->post()) &&
            $this->_orderNp->load(Yii::$app->request->post()) &&
            $this->_customer->load(Yii::$app->request->post())) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                $this->_cart->validateOrder();
                $this->_order->cache_sum_total = $this->_cart->cacheSumTotal;
                if (!$this->_order->save()) {
                    Is::errors($this->_order->errors);
                }
                $this->_orderNp->saveNewFromSite($this->_order->id);

                $this->_customer->order_id = $this->_order->id;
                if (!$this->_customer->save()) {
                    Is::errors($this->_customer->errors);
                }
                OrderItem::saveItem($this->_cart->items, $this->_order->id);

                if ($this->_order->payment_method == Order::PAYMENT_METHOD_PAY_ONLINE_NEW) {
                    $session = Yii::$app->session;
                    $session->open();
                    $_SESSION['order_id'] = $this->_order->id;
                    $transaction->commit();
                    return $this->redirect(['/interkassa', 'order_id' => $this->_order->id]);
                }

                $transaction->commit();

                $this->_cart->clearCart();
                $order = Order::findOne($this->_order->id);
                $cart = new Cart();
                $cart->build(new CartData($order));
                new OrderNotification($order, $cart);

                $admin = new TAdmin();
                $admin->sendOrderInGroup($order, $cart);

                Yii::$app->session->setFlash('success', 'Ваш заказ принят!');
                Yii::$app->session->set('ShowTrackingTradeJS',true);
                return $this->redirect([Order::timeUrl('success', $this->_order)]);
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
                $transaction->rollback();
            }
        }

        return $this->render('index', [
            'order' => $this->_order,
            'orderNp' => $this->_orderNp,
            'customer' => $this->_customer,
            'cart' => $this->_cart,
            'page' => SitePage::page('order'),
            'delivery' => Delivery::forOrder()
        ]);
    }

    private function setOrder()
    {
        if (isset($_SESSION['order_id']) && $this->_order = Order::findOne($_SESSION['order_id'])) {
            $this->_orderNp = $this->_order->np;
            $this->_orderNp->scenario = 'new_order_site';
            $this->_customer = $this->_order->customer;
            return true;
        }
        $this->_order = new Order();
        $this->_order->customer_id = Customer::CUSTOMER_SITE;
        $this->_order->payment_method = Order::PAYMENT_METHOD_UNKNOWN;
        $this->_order->status = Order::STATUS_NEW_SITE;
        $this->_order->source = Order::SOURCE_SITE;
        $this->_orderNp = new OrderNp();
        $this->_orderNp->scenario = 'new_order_site';
        $this->_customer = new OrderCustomer();
        $this->_order->save(false);
        $orderTracking = new OrderTracking();
        $orderTracking->order_id = $this->_order->id;
        $orderTracking->new_status = $this->_order->status;
        $orderTracking->save(false);
    }

//    private function payFondy($order)
//    {
//        try {
//            $this->_fondyService = Yii::createObject([
//                'class' => FondyService::class
//            ]);
//            $this->_fondyService->fondyModel = Fondy::setModel($order->id);
//            $this->_fondyService->platformData = new PlatformData(Customer::CUSTOMER_SITE, $order);
//            $this->_fondyService->cart = $this->_cart;
//            $this->_fondyService->checkout();
//            return $this->redirect($this->_fondyService->urlCheckout);
//        } catch (\Exception $e) {
//            Yii::$app->errorHandler->logException($e);
//            Yii::$app->session->setFlash('error', $e->getMessage());
//            return $this->redirect(Yii::$app->request->referrer);
//        }
//    }

    public function actionNpBot($order_id)
    {
        $this->layout = 'main-np-bot';
        $orderNp = OrderNp::findOne($order_id);
        $orderNp->scenario = 'new_order_site';
        $platformData = new PlatformData($orderNp->order->customer->bot->platform);
        if ($orderNp->load(Yii::$app->request->post()) && $orderNp->validate()) {
            try {
                $orderNp->setDataFromSession();
                $orderNp->buildData();
                $orderNp->save();
                $orderNp->order->delivery = Delivery::DELIVERY_NP;
                $orderNp->order->save();
                if ($orderNp->order->customer->bot->platform == Bot::TELEGRAM) {
                    Yii::$app->tm->customer = $orderNp->order->customer;
                    Yii::$app->tm->platformId = $orderNp->order->customer->platform_id;
                    $admin = new TAdmin();
                    $admin->orderPayMenu();
                }
                if ($orderNp->order->customer->bot->platform == Bot::VIBER) {
                    Yii::$app->vb->customer = $orderNp->order->customer;
                    Yii::$app->vb->platformId = $orderNp->order->customer->platform_id;
                    $admin = new VAdmin();
                    $admin->orderPayMenu();
                }
                Search::delSessionNp();
                return $this->redirect($platformData->redirectInBot);
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect(Yii::$app->request->referrer);
            }
        }
        $orderNp->city = null;
        return $this->render('np-bot', [
            'orderNp' => $orderNp,
            'linkBackInBot' => $platformData->linkBackInBot,
            'listWarehouses' => [],
        ]);
    }

}

