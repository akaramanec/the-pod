<?php

namespace backend\modules\shop\controllers;

use backend\modules\admin\models\AuthLogger;
use backend\modules\bot\models\BotLogger;
use backend\modules\bot\src\ApiNp;
use backend\modules\bot\src\ApiProduct;
use backend\modules\bot\src\DocumentNp;
use backend\modules\bot\src\SenderNp;
use backend\modules\customer\models\Customer;
use backend\modules\shop\models\Delivery;
use backend\modules\shop\models\OrderItem;
use backend\modules\shop\models\OrderNp;
use backend\modules\shop\models\OrderTracking;
use backend\modules\shop\models\ProductMod;
use backend\modules\shop\models\search\ProductModSearch;
use frontend\models\cart\Cart;
use frontend\models\cart\CartData;
use frontend\models\OrderCustomer;
use src\helpers\Date;
use src\services\Role;
use src\validators\Is;
use Yii;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\search\OrderSearch;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class OrderController extends Controller
{
    public $layout = 'order';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    public function actionIndex()
    {
        AuthLogger::saveModel();
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (!Role::check('all-orders-view')) {
            $searchModel->query->andWhere(['or',
                ['order.status' => array_keys(Order::statusesNew())],
                ['order.manager_id' => Yii::$app->user->id]
            ]);
        } else {
            $searchModel->query->andWhere(['order.status' => array_merge(array_keys(Order::statusesAll()), array_keys(Order::statusesNew()))]);
        }
        $searchModel->query->andWhere(['order.payment_method' => array_keys(Order::paymentsForOrders())]);
        if ($searchModel->blogger) {
            $customer = Customer::findOne($searchModel->blogger);
            $searchModel->query->andWhere([
                'order.customer_id' => $customer->blog->customerId
            ]);
        }
        $dataProvider->setPagination(['pageSize' => Yii::$app->request->cookies->getValue('pagination')]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        AuthLogger::saveModel();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order = new Order();
            $order->customer_id = Customer::CUSTOMER_SITE;
            $order->status = Order::STATUS_NEW_ADMIN;
            $order->payment_method = Order::PAYMENT_METHOD_UNKNOWN;
            $orderNp = new OrderNp();
            $orderNp->scenario = 'new_order_site';
            $customer = new OrderCustomer();
            $order->save(false);
            $orderTracking = new OrderTracking();
            $orderTracking->order_id = $order->id;
            $orderTracking->new_status = $order->status;
            $orderTracking->save(false);
            if ($order->load(Yii::$app->request->post()) && $orderNp->load(Yii::$app->request->post()) && $customer->load(Yii::$app->request->post())) {
                if (!$order->save()) {
                    Is::errors($order->errors);
                }
                $orderNp->saveNewFromSite($order->id);

                $customer->order_id = $order->id;
                if (!$customer->save()) {
                    Is::errors($customer->errors);
                }
                $transaction->commit();
                return $this->redirect(['update', 'id' => $order->id]);
            }
        } catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
            $transaction->rollback();
        }

        return $this->render('create', [
            'order' => $order,
            'orderNp' => $orderNp,
            'customer' => $customer,
            'delivery' => Delivery::forOrder()
        ]);
    }

    public function actionUpdate($id)
    {
        AuthLogger::saveModel();
        $model = $this->findModel($id);
        $model->actionByStatus();
        $cart = new Cart();
        $cart->build(new CartData($model));
        if ($model->load(Yii::$app->request->post())) {
            $model->cache_sum_total = $cart->cacheSumTotal;
            $model->save();
            AuthLogger::saveModel(['status' => $model->status]);
            $model->actionInWork();
            $model->actionCloseSuccess();
            return $this->redirect(['update', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
            'cart' => $cart,
        ]);
    }

    public function actionNp($id)
    {
        AuthLogger::saveModel();
        $order = $this->findModel($id);
        $orderNp = OrderNp::findOne($id);
        $orderNp->departure_date = Date::minusPlus(Yii::$app->common->datetimeNow, '+30min');
        $orderNp->update(false);
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $apiNp = new ApiNp();
            $senderNp = new SenderNp($apiNp);
            if ($orderNp === null) {
                throw new NotFoundHttpException('Отсутствует модель ттн.');
            }
            $orderNp->scenario = 'ttn';
            $cart = new Cart();
            $cart->build(new CartData($order));
            if ($order->load(Yii::$app->request->post()) && $order->validate() &&
                $orderNp->load(Yii::$app->request->post()) && $orderNp->validate() &&
                $order->customer->load(Yii::$app->request->post()) && $order->customer->validate()) {
                $order->save(false);
                $order->customer->save(false);
                $orderNp->dateFormatForSave();
                $orderNp->comparisonWithCurrentDate();
                $orderNp->save(false);
                $orderNp->internetDocumentSave($apiNp);
                $transaction->commit();
                return $this->redirect(['np', 'id' => $order->id]);
            }
        } catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
            $transaction->rollback();
            BotLogger::save_input(['actionNp', $e->getMessage()]);
            BotLogger::save_input(['actionNp', Yii::$app->common->error]);
        }

        return $this->render('np', [
            'order' => $order,
            'cart' => $cart,
            'orderNp' => $orderNp,
            'senderNp' => $senderNp,
        ]);
    }

    public function actionDelete($id)
    {
        AuthLogger::saveModel();
        if (Role::check('order-delete')) {
            $order = $this->findModel($id);
            $apiNp = new ApiNp();
            $documentNp = new DocumentNp($apiNp);
            if ($order->np->document && isset($order->np->document['data'][0]['Ref'])) {
                $documentNp->delete($order->np->document['data'][0]['Ref']);
            }
            $api = new ApiProduct();
            $api->leftoversDelete($order);
            $order->delete();
        }
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        $model = Order::find()
            ->where(['id' => $id])
            ->limit(1)->one();
        if ($model) {
            return $model;
        }
        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }

    public function actionSearchAjax($q)
    {
        $q = trim($q);
        $searchModel = new ProductModSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $searchModel->query->andWhere([
            'mod.status' => ProductMod::STATUS_ACTIVE
        ]);
        if ((int)$q) {
            $searchModel->query->andWhere([
                'product.code' => (int)$q,
            ]);
        } else {
            $searchModel->query->andFilterWhere(['like', 'product.name', $q]);
        }
        $dataProvider->setPagination(['pageSize' => 30]);
        return $this->renderAjax('_search_ajax', [
            'models' => $dataProvider->getModels(),
        ]);
    }

    public function actionCheckStatusAjax()
    {
        if (Yii::$app->request->isAjax) {
            $order_id = Yii::$app->request->post('order_id');
            return Json::encode(['status' => Order::findOne($order_id)->status]);
        }
        return false;
    }

    public function actionAddProduct($search_id, $model_id)
    {
        $orderItem = new OrderItem();
        $orderItem->mod_id = $search_id;
        $orderItem->order_id = $model_id;
        $orderItem->save();
        $this->editCacheSumTotal($orderItem->order);
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDeleteProduct($mod_id, $order_id)
    {
        $orderItem = OrderItem::findOne(['mod_id' => $mod_id, 'order_id' => $order_id]);
        $orderItem->delete();
        $this->editCacheSumTotal($orderItem->order);
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionEditMod($mod_id, $order_id)
    {
        $orderItem = OrderItem::findOne(['mod_id' => $mod_id, 'order_id' => $order_id]);
        if ($orderItem->load(Yii::$app->request->post()) && $orderItem->save()) {
            $this->editCacheSumTotal($orderItem->order);
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->renderAjax('_edit-mod', [
            'orderItem' => $orderItem
        ]);
    }

    private function editCacheSumTotal($order)
    {
        $cart = new Cart();
        $cart->build(new CartData($order));
        $order->cache_sum_total = $cart->cacheSumTotal;
        $order->save();
    }

}
