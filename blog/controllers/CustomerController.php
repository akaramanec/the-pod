<?php

namespace blog\controllers;

use backend\modules\bot\models\BotLogger;
use backend\modules\customer\models\Customer;
use backend\modules\customer\models\search\CustomerBloggerSearch;
use blog\models\CacheBloggerFixed;
use src\helpers\DieAndDumpHelper;
use Yii;
use backend\modules\customer\models\search\CustomerSearch;
use yii\db\Expression;
use yii\web\Controller;

class CustomerController extends Controller
{
    public $layout = 'base';

    public function actionIndex()
    {
        $customer = Customer::findOne(Yii::$app->user->identity->id);
        $searchModel = new CustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $searchModel->query->andFilterWhere([
            'customer.id' => $customer->blog->customerId ? $customer->blog->customerId : -1
        ]);
        $dataProvider->setPagination(['pageSize' => Yii::$app->request->cookies->getValue('pagination')]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'customer' => $customer,
        ]);
    }

    public function actionTest()
    {
//        $dimaAll = 8;
//        $dimaU = 2;
//        $dimaA = $dimaAll - $dimaU;
//        $mashaAll = 17;
//        $mashaU = 6;
//        $mashaA = $mashaAll - $mashaU;
//
//        $needA = $dimaA + $mashaA;
////        $needA = $mashaA;
//        $customersA = $this->getCustomers([Customer::STATUS_ACTIVE, Customer::STATUS_SUBSCRIBED], $needA);
//        foreach ($customersA as $customer) {
//                if ($dimaA) {
//                    $customer->parent_id = 77;
//                    $customer->save();
//                    $dimaA--;
//                    continue;
//                }
//                if ($mashaA) {
//                    $customer->parent_id = 49227;
//                    $customer->save();
//                    $mashaA--;
//                }
//        }
//
//        $needU = $dimaU + $mashaU;
////        $needU = $mashaU;
//        $customersU = $this->getCustomers([Customer::STATUS_UNSUBSCRIBED], $needU);
//        foreach ($customersU as $customer) {
//            if ($dimaU) {
//                $customer->parent_id = 77;
//                $customer->save();
//                $dimaU--;
//                continue;
//            }
//            if ($mashaU) {
//                $customer->parent_id = 49227;
//                $customer->save();
//                $mashaU--;
//            }
//        }
//        DieAndDumpHelper::printing('done');
    }

    /**
     * @param array $statuses
     * @param int $count
     * @return array|Customer[]|\yii\db\ActiveRecord[]
     */
    private function getCustomers(array $statuses, int $count): array
    {
        $query = Customer::find()
            ->alias('customer')
            ->joinWith(['bot AS bot']);
        $query->andFilterWhere([
            'customer.status' => $statuses
        ]);
        $query->andFilterWhere([
            'customer.parent_id' => 46056
        ]);
        $query->andFilterWhere(['>', 'customer.created_at', '2021-12-02 00:00:00']);
        $query->andFilterWhere(['<', 'customer.created_at', '2021-12-25 23:59:59']);
        return $query->limit($count)->all();
    }

}
