<?php

namespace backend\modules\shop\controllers;

use backend\modules\shop\models\Category;
use backend\modules\shop\models\Product;
use backend\modules\shop\models\search\ProductSearch;
use src\helpers\DieAndDumpHelper;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ProductController
 * @package app\controllers
 */
class ProductController extends Controller
{
    /**
     * @inheritdoc
     */
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

    /**
     * @param $base_id
     * @return string|Response
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $page = Yii::$app->request->get('page', '1');

        return $this->render('index', compact('searchModel', 'dataProvider', 'page'));
    }


    public function actionCreate()
    {
        $model = $this->findModel('new');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Product success created'));
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * @return bool
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->post('id');
        $base_id = Yii::$app->request->post('base_id');
        $product = Product::findOne($id);

        if ($product && Base::isOwner($base_id)) {
            if ($product->image && file_exists(Yii::getAlias('@app/web' . $product->image)) && strripos($product->image, 'img/demo/placeholder-product.jpg') === false) {
                unlink(Yii::getAlias('@app/web' . $product->image));
            }
            // delete join product poster
            $poster_item = PosterItem::findOne(['platform_id' => $product->id, 'base_id' => $base_id, 'type' => PosterItem::TYPE_PRODUCT]);
            if ($poster_item) {
                $poster_item->delete();
            }
            $product->delete();
            return true;
        }

        return false;
    }

    protected function findModel($id)
    {
        if ($id && ($model = Product::findOne($id)) !== null) {
            return $model;
        }
        if ($id == 'new') {
            return new Product();
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
