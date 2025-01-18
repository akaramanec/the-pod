<?php

namespace backend\modules\admin\controllers;

use backend\modules\admin\models\AuthAssignment;
use backend\modules\admin\models\Password;
use Yii;
use backend\modules\admin\models\AuthAdmin;
use backend\modules\admin\models\AuthAdminSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class AdminController extends Controller
{
    public $layout = 'base';

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

    public function actionIndex()
    {
        $searchModel = new AuthAdminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setPagination(['pageSize' => Yii::$app->request->cookies->getValue('pagination')]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new AuthAdmin();
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->auth_key = Yii::$app->security->generateRandomString();
                $model->password = Yii::$app->security->generatePasswordHash($model->password);
                if ($model->save()) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'Админ добавлен');
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
                $transaction->rollback();
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Updates are successful'));
            return $this->redirect(['update', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionChangePassword($id)
    {
        $model = new Password();
        if ($model->load(Yii::$app->request->post()) && $model->changePassword($id)) {
            return $this->redirect(['/admin/login/logout']);
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionDelete($id)
    {
        try {
            $model = $this->findModel($id);
            $model->delete();
        } catch (\Exception $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    protected function findModel($id)
    {
        if (($model = AuthAdmin::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Запрошенная страница не существует.');
    }

}
