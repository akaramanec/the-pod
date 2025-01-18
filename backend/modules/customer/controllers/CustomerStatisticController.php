<?php

namespace backend\modules\customer\controllers;

use backend\controllers\BaseController;
use backend\modules\customer\models\search\CustomerStatisticSearch;
use src\services\CustomerStatistic;
use Yii;

class CustomerStatisticController extends BaseController
{
    public $layout = 'base';

    public function actionIndex()
    {
        $this->js('customer');
        $searchModel = new CustomerStatisticSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setPagination(['pageSize' => Yii::$app->request->cookies->getValue('pagination')]);

        $customerStatistic = new CustomerStatistic($dataProvider->getModels());
        $dataProvider->setModels($customerStatistic->models);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


}
