<?php

namespace backend\modules\shop\controllers;

use src\helpers\DieAndDumpHelper;
use Yii;
use backend\modules\shop\models\ProductMod;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class ProductModController extends Controller
{
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

    public function actionCreate($productId)
    {
        $model = new ProductMod();
        $model->product_id = $productId;

        if (Yii::$app->request->isPost) {
            $model->uuidAndSlugGenerate();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['product/update', 'id' => $productId]);
        }

        if ($model->hasErrors()) {
            DieAndDumpHelper::dd($model->errors);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['product/update', 'id' => $model->product_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $productId = $model->product_id;
        $model->delete();

        return $this->redirect(['product/update', 'id' => $productId]);
    }

    protected function findModel($id)
    {
        if (($model = ProductMod::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}