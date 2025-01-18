<?php

namespace backend\modules\customer\controllers;

use backend\controllers\BaseController;
use backend\modules\admin\models\AuthLogger;
use backend\modules\customer\models\Customer;
use src\helpers\DatePeriodSelectorHelper;
use blog\models\CustomerBlog;
use src\helpers\DieAndDumpHelper;
use Yii;
use backend\modules\customer\models\form\CustomerForm;
use backend\modules\customer\models\search\CustomerSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class CustomerController extends BaseController
{
    public $layout = 'base';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        AuthLogger::saveModel();
        $this->js('customer');
        $dateRange = DatePeriodSelectorHelper::getPeriod();
        $searchModel = new CustomerSearch($dateRange);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $searchModel->query->joinWith(['parent AS parent']);
        $dataProvider->setPagination(['pageSize' => Yii::$app->request->cookies->getValue('pagination')]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dateRange' => $dateRange
        ]);
    }


    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->renderAjax('_view', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        AuthLogger::saveModel();
        $model = $this->findModel($id);
        $customerBlog = CustomerBlog::getModel($model);
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $model->saveTags();
                if ($model->blogger == Customer::BLOGGER_TRUE) {
                    $customerBlog::saveModel($customerBlog, $model);
                }
                $transaction->commit();
                return $this->redirect(['update', 'id' => $model->id]);
            }
        } catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            DieAndDumpHelper::dd($e->getMessage(), $e->getTraceAsString());
            Yii::$app->session->setFlash('error', $e->getMessage());
            $transaction->rollback();
            return $this->redirect(Yii::$app->request->referrer);
        }
        $model->getTagsId();
        return $this->render('update', [
            'model' => $model,
            'customerBlog' => $customerBlog,
        ]);
    }

    public function actionDelete($id)
    {
        AuthLogger::saveModel();
        $this->findModel($id)->delete();
        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function findModel($id)
    {
        if (($model = CustomerForm::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
