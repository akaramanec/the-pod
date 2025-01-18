<?php

namespace backend\modules\media\controllers;

use backend\modules\customer\models\Newsletter;
use backend\modules\media\models\Files;
use backend\modules\media\models\FileInit;
use src\helpers\DieAndDumpHelper;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;


class FileController extends Controller
{
    public $layout = false;
    private $model;

    public function actionSave($id, $model)
    {
        $this->setModel($model);
        $model = $this->model::findOne($id);
        if ($model->load(Yii::$app->request->post(), '') && $model->save(false)) {
            return Json::encode(['status' => 'ok']);
        }
        return Json::encode(['error' => $model->getErrors() != [] ? $model->getErrors() : 'unknown error']);
    }

    public function actionDelete($id, $model)
    {
        $this->setModel($model);
        $entityFile = Yii::createObject([
            'class' => FileInit::class,
            'entity' => $this->model::ENTITY
        ]);
        $entityFile->deleteMainFile($id);
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
        $model = str_replace('%5C', '\\', $model);
        $this->model = new $model();
    }

    public function actionUpdate($id)
    {
        $file = Files::findOne($id);
        if ($file->load(Yii::$app->request->post()) && $file->save()) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->renderAjax('_update', [
            'file' => $file,
        ]);
    }

    public function actionTest()
    {
        $newsletter = Newsletter::find()->where(['id' => 48])->limit(1)->one();
        \src\helpers\DieAndDumpHelper::dd(strtoupper((new \ReflectionClass($newsletter))->getShortName()));
//        echo Url::to(['/media/file/file-save', 'id' => 48, 'model' => get_class(new Newsletter())]);
//        return $this->redirect(Yii::$app->request->referrer);
    }
}
