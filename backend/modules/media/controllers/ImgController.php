<?php

namespace backend\modules\media\controllers;

use backend\modules\media\models\Images;
use backend\modules\media\models\ImgInit;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;


class ImgController extends Controller
{
    public $layout = false;
    private $model;

    public function actionImgSave($id, $model)
    {
        $this->setModel($model);
        $model = $this->model::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionDeleteMainImg($entity_id, $entity)
    {
        $entityImg = Yii::createObject([
            'class' => ImgInit::class,
            'entity' => $entity
        ]);
        $entityImg->deleteMainImg($entity_id);
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionDeleteImg($entity_id, $entity)
    {
        $img = Images::findOne(['entity_id' => $entity_id, 'entity' => $entity]);
        $entityImg = Yii::createObject([
            'class' => ImgInit::class,
            'entity' => $img->entity,
        ]);
        $img->delete();
        @unlink($entityImg->path . $img->entity_id . DIRECTORY_SEPARATOR . $img->img);
        return $this->redirect(Yii::$app->request->referrer);
    }


    public function actionValidate($model)
    {
        $this->setModel($model);
        if (Yii::$app->request->isAjax && $this->model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($this->model);
        }
    }

    private function setModel($model)
    {
        $this->model = new $model();
    }
}
