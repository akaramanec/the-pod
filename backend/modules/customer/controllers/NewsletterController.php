<?php

namespace backend\modules\customer\controllers;

use backend\modules\customer\models\CustomerTag;
use backend\modules\customer\models\Newsletter;
use backend\modules\customer\models\search\CustomerSearchNewsletter;
use backend\modules\customer\models\search\NewsletterSearch;
use backend\modules\shop\service\analytics\helpers\AnalyticsOrderHelper;
use src\helpers\Date;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class NewsletterController extends Controller
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
        $searchModel = new NewsletterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setPagination(['pageSize' => Yii::$app->request->cookies->getValue('pagination')]);
        $searchModel->query->andFilterWhere([
            'status' => [Newsletter::STATUS_SEND, Newsletter::STATUS_IN_WORK, Newsletter::STATUS_SENT]
        ]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = Newsletter::getModel();
        return $this->redirect(['update', 'id' => $model->id]);
    }

    public function actionCreateByAnalytics()
    {
        $getData = Yii::$app->request->get();
        $dateFrom = $getData['dateFrom'] ?? null;
        $dateTo = $getData['dateTo'] ?? null;
        $tagTitle = $getData['tag'] ?? null;

        if (!isset($dateFrom) || !isset($dateTo) || !isset($tagTitle)) {
            throw new NotFoundHttpException;
        }

        $tag = CustomerTag::getTagModel($tagTitle);
        AnalyticsOrderHelper::addCustomersTag($tag->id, $dateFrom, $dateTo);
        $model = Newsletter::getModel();

        return $this->redirect(['update', 'id' => $model->id, 'tagId' => $tag->id]);
    }

    /**
     * @param int $id
     * @param int|null $tagId
     * @return void|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id, int $tagId = null)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->checkContent();
                $model->dateDeparture();
                $model->status = Newsletter::STATUS_SEND;
                $model->setSetting();
                $model->save();
                return $this->redirect(['index']);
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        $model->setSetting();

        /* tag from analytics */
        if (!empty($tagId) && array_search($tagId, $model->tagsId) === false) {
            $model->tagsId[] = $tagId;
        }

        $search = new CustomerSearchNewsletter($model);
        $search->setDataProvider();
        $model->qtyCustomer = $search->dataProvider->count;
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSaveAjax()
    {
        $params = json_decode(Yii::$app->request->post('params'));
        $newsletter = Newsletter::findOne($params->newsletter_id);
        $newsletter->text = $params->text;
        $newsletter->date_departure = $params->date_departure;
        $newsletter->sendTelegram = $params->sendTelegram;
        $newsletter->sendViber = $params->sendViber;
        $newsletter->sendEmail = $params->sendEmail;
        $newsletter->sendNow = $params->sendNow;
        $newsletter->tagsId = $params->tagsId;
        $newsletter->customerBlogger = $params->customerBlogger;
        $newsletter->activeCustomer = $params->activeCustomer;
        $newsletter->subscribedCustomer = $params->subscribedCustomer;
        $newsletter->notCustomerBlogger = $params->notCustomerBlogger;

        $search = new CustomerSearchNewsletter($newsletter);
        $search->setDataProvider();
        $newsletter->buildSetting();
        $newsletter->save();
        return $search->dataProvider->count;
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Newsletter::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
