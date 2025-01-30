<?php

namespace backend\modules\shop\controllers;

use backend\modules\shop\models\Category;
use backend\modules\shop\models\search\CategorySearch;
use src\helpers\DieAndDumpHelper;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class ProductController
 * @package app\controllers
 */
class CategoryController extends Controller
{

    /**
     * @return array[]
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string|Response
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $page = Yii::$app->request->get('page', '1');

        return $this->render('index', compact('searchModel', 'dataProvider', 'page'));
    }


    public function actionCreate()
    {
        $model = $this->findModel('new');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Category success created'));
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

    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        $category = Category::findOne($id);

        if ($category) {
            if ($category->img && file_exists(Yii::getAlias('@app/web' . $category->img)) && strripos($category->img, 'img/demo/placeholder-product.jpg') === false) {
                unlink(Yii::getAlias('@app/web' . $category->img));
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Category success deleted'));
            $category->delete();
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if ($id && ($model = Category::findOne($id)) !== null) {
            return $model;
        }
        if ($id == 'new') {
            return new Category();
        }
        throw new NotFoundHttpException('Запрашиваемая страница не существует.');
    }
}
