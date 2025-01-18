<?php

namespace backend\modules\shop\controllers;

use backend\controllers\BaseController;
use backend\modules\shop\models\Attribute;
use backend\modules\shop\models\AttributeValue;
use backend\modules\shop\models\search\AttributeSearch;
use Yii;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class AttributeController extends BaseController
{
    public $layout = 'base';

    public function actionIndex($id = null, $attribute_value_id = null)
    {
        $model = null;
        $attribute_value = null;

        $searchModel = new AttributeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->setPagination(['pageSize' => false]);

        if ($id) {
            $model = $this->findModel($id);
        }
        if ($attribute_value_id) {
            $attribute_value = AttributeValue::findOne($attribute_value_id);
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'attribute_value' => $attribute_value,
        ]);
    }

    public function actionCreate()
    {
        $model = new Attribute();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->render('create', [
            'model' => $model,
            'title' => 'Добавить атрибут'
        ]);
    }

    public function actionCreateValue($id)
    {
        $model = $this->findModel($id);
        $attributeValue = new AttributeValue();
        $attributeValue->attribute_id = $model->id;
        if ($attributeValue->load(Yii::$app->request->post()) && $attributeValue->save()) {
            return $this->redirect(Url::to(['update', 'id' => $model->id]));
        }
        return $this->render('create-value', [
            'model' => $attributeValue,
            'title' => 'Добавить значение атрибута: ' . $model->name
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->render('update', [
            'model' => $model,
            'title' => 'Изменить атрибут: ' . $model->name
        ]);
    }

    public function actionUpdateValue($id)
    {
        $attributeValue = AttributeValue::findOne($id);
        if ($attributeValue->load(Yii::$app->request->post()) && $attributeValue->save()) {
            return $this->redirect(['index', 'id' => $attributeValue->shopAttribute->id]);
        }
        return $this->render('update-value', [
            'model' => $attributeValue,
            'title' => 'Изменить начение атрибута: ' . $attributeValue->name
        ]);
    }

    public function actionSort($sort)
    {
        foreach (Json::decode($sort) as $key => $id) {
            Attribute::updateAll(['sort' => $key], ['=', 'id', $id]);
        }
    }

    public function actionSortValue($sort)
    {
        foreach (Json::decode($sort) as $key => $id) {
            AttributeValue::updateAll(['sort' => $key], ['=', 'id', $id]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    public function actionSearch($q)
    {
        $searchModel = new AttributeSearch();
        $dataProvider = $searchModel->search(['AttributeSearch' => ['name' => $q]]);
        $dataProvider->setPagination(['pageSize' => false]);
        return $this->renderAjax('_search_ajax', [
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Attribute::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
