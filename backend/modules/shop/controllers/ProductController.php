<?php

namespace backend\modules\shop\controllers;

use backend\modules\shop\models\search\ProductSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
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
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['list', 'change', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                        'denyCallback' => function ($rule, $action) {
                            return $action->controller->redirect(['user/login']);
                        },
                    ],
                ],

            ],
        ];
    }

    /**
     * @param $base_id
     * @return string|Response
     */
    public function actionIndex($base_id)
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $base_id);
        $page = Yii::$app->request->get('page', '1');

        return $this->render('index', compact('searchModel', 'dataProvider', 'page'));
    }

    /**
     * @param $action
     * @param $base_id
     * @param int $id
     * @param int $last_page
     * @return string|Response
     */
    public function actionChange($action, $base_id, $id = 0, $last_page = 1)
    {
        if (Base::isOwner($base_id)) {

            if (!$model = ProductForm::findOne(['id' => $id, 'base_id' => $base_id])) {
                $model = new ProductForm();
            }
            if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
                if (method_exists($model, $action) && $model->$action()) {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Product success added'));
                    return $this->redirect(['product/list', 'base_id' => $base_id, 'page' => $last_page]);
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Error while saving'));
                }
            }

            return $this->render('edit', compact('model', 'base_id'));
        }

        return $this->redirect(['bots/index']);
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
}
