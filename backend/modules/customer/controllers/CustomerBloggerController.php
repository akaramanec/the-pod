<?php

namespace backend\modules\customer\controllers;

use backend\controllers\BaseController;
use backend\modules\admin\models\AuthLogger;
use backend\modules\bot\models\Logger;
use backend\modules\customer\models\Customer;
use backend\modules\customer\models\search\CustomerBloggerSearch;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\OrderPayBloggerFixed;
use backend\modules\shop\models\search\OrderSearch;
use blog\models\CacheBlogger;
use blog\models\CacheBloggerFixed;
use src\helpers\Date;
use src\helpers\DieAndDumpHelper;
use Yii;
use yii\helpers\Json;

class CustomerBloggerController extends BaseController
{
    public $layout = 'base';

    public function actionIndex()
    {
        AuthLogger::saveModel();
        if (!isset(Yii::$app->request->queryParams['dateFrom']) && !isset(Yii::$app->request->queryParams['dateTo'])) {
            unset($_SESSION['dateFrom']);
            unset($_SESSION['dateTo']);
        }
        if (isset(Yii::$app->request->queryParams['dateFrom']) && isset(Yii::$app->request->queryParams['dateTo'])) {
            $_SESSION['dateFrom'] = Yii::$app->request->queryParams['dateFrom'];
            $_SESSION['dateTo'] = Yii::$app->request->queryParams['dateTo'];
        }

        $this->js('customer');
        $searchModel = new CustomerBloggerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setPagination(['pageSize' => Yii::$app->request->cookies->getValue('pagination')]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        AuthLogger::saveModel();
        $customer = Customer::findOne($id);
        $customer->setAllBloggerData();
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $searchModel->query->andFilterWhere(['order.status' => Order::STATUS_CLOSE_SUCCESS]);
        $searchModel->query->andWhere([
            'order.customer_id' => $customer->blog->customerId
        ]);
        $dataProvider->setPagination(['pageSize' => Yii::$app->request->cookies->getValue('pagination')]);
        return $this->render('view', [
            'customer' => $customer,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPayBlogger($order_id, $blogger_id)
    {
        AuthLogger::saveModel();
        try {
            $order_id = Json::decode($order_id);
            if (!$order_id) {
                throw new \Exception('Не выбран не один элемент');
            }
            $cacheBlogger = new CacheBlogger();
            $cacheBlogger->setPay($order_id, $blogger_id);
            Yii::$app->session->setFlash('success', 'Данные успешно сохранились');
            return $this->redirect(Yii::$app->request->referrer);
        } catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionPayBloggerWholesale($blogger_id)
    {
        AuthLogger::saveModel();
        try {
            $blogger_id = Json::decode($blogger_id);
            if (!$blogger_id) {
                throw new \Exception('Не выбран не один элемент');
            }
            $cacheBlogger = new CacheBlogger();
            $cacheBlogger->setPayWholesale($blogger_id);
            Yii::$app->session->setFlash('success', 'Данные успешно сохранились');
            return $this->redirect(Yii::$app->request->referrer);
        } catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionPayBloggerFixed($customer_id)
    {
        $model = new OrderPayBloggerFixed();
        $model->customer_id = $customer_id;
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_order_pay_blogger_fixed', [
                'model' => $model
            ]);
        }
        AuthLogger::saveModel();
        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $cacheBloggerFixed = new CacheBloggerFixed();
                $cacheBloggerFixed->setCache(Customer::findOne($customer_id));
                $model->setData();
                $model->save();
                Yii::$app->session->setFlash('success', 'Данные успешно сохранились');
                return $this->redirect(Yii::$app->request->referrer);
            }
        } catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionDeleteStatisticDate()
    {
        unset($_SESSION['dateFrom']);
        unset($_SESSION['dateTo']);
        return $this->redirect(preg_replace('/dateFrom=.{10}&dateTo=.{10}/', '', Yii::$app->request->referrer));
    }
}
