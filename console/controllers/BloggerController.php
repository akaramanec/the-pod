<?php

namespace console\controllers;

use blog\models\CacheBloggerFixed;
use Yii;
use backend\modules\customer\models\Customer;
use blog\models\CacheBlogger;
use yii\console\Controller;

class BloggerController extends Controller
{

    public function actionIndex()
    {
        $q = Customer::find()
            ->where(['in', 'status', [Customer::STATUS_ACTIVE, Customer::STATUS_SUBSCRIBED]])
            ->andWhere(['blogger' => Customer::BLOGGER_TRUE])
            ->with(['blog']);
        foreach ($q->each() as $customer) {
            $cacheBlogger = new CacheBlogger();
            $cacheBlogger->setCache($customer);
        }
    }

    public function actionCacheBloggerFixed()
    {
        $q = Customer::find()
            ->where(['in', 'status', [Customer::STATUS_ACTIVE, Customer::STATUS_SUBSCRIBED]])
            ->andWhere(['blogger' => Customer::BLOGGER_TRUE])
            ->with(['blog']);
        foreach ($q->each() as $customer) {
            $cacheBloggerFixed = new CacheBloggerFixed();
            $cacheBloggerFixed->setCache($customer);
        }
    }
}
