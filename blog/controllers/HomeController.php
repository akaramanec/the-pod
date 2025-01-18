<?php

namespace blog\controllers;

use backend\modules\customer\models\Customer;
use backend\modules\shop\models\OrderItem;
use backend\modules\shop\models\ProductMod;
use backend\modules\shop\models\search\ProductModSearch;
use blog\models\CustomerLinkReferral;
use Yii;

class HomeController extends \yii\web\Controller
{
    public $layout = 'base';

    public function actionIndex()
    {
        $customer = Customer::findOne(Yii::$app->user->identity->id);
        return $this->render('index', [
            'customer' => $customer
        ]);
    }

    public function actionSearchAjax($q)
    {
        $q = trim($q);
        $searchModel = new ProductModSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $searchModel->query->andWhere([
            'mod.status' => ProductMod::STATUS_ACTIVE
        ]);

        $searchModel->query->andFilterWhere(['or',
            ['like', 'product.code', $q],
            ['like', 'product.name', $q],
            ['like', 'product.slug', $q]
        ]);
        $dataProvider->setPagination(['pageSize' => 30]);
        return $this->renderAjax('@backend/modules/shop/views/order/_search_ajax.php', [
            'models' => $dataProvider->getModels(),
        ]);
    }

    public function actionAddProduct($search_id, $model_id)
    {
        try {
            $mod = ProductMod::findOne($search_id);
            $clr = new CustomerLinkReferral();
            $clr->customer_id = Yii::$app->user->identity->id;
            $clr->modId = $search_id;
            $clr->productCode = $mod->product->code;
            $clr->checkExistModId();
            $clr->buildData();
            $clr->save();
            return $this->redirect(Yii::$app->request->referrer);
        } catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionDeleteProduct($id)
    {
        $m = CustomerLinkReferral::findOne($id);
        $m->delete();
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDemo()
    {
        return $this->render('demo');
    }
}
