<?php

namespace blog\controllers;

use backend\modules\customer\models\Customer;
use backend\modules\shop\models\OrderPayBlogger;
use src\helpers\CategoryHelp;
use Yii;
use backend\modules\shop\models\Order;
use backend\modules\shop\models\search\OrderSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class OrderController extends Controller
{

    public $layout = 'base';

    public function actionIndex()
    {
        $customer = Customer::findOne(Yii::$app->user->identity->id);
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $searchModel->query->andFilterWhere(['order.status' => Order::STATUS_CLOSE_SUCCESS]);
        $searchModel->query->andWhere([
            'order.customer_id' => $customer->blog->customerId
        ]);
        $dataProvider->setPagination(['pageSize' => Yii::$app->request->cookies->getValue('pagination')]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'customer' => $customer,
        ]);
    }


}
