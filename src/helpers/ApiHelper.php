<?php

namespace src\helpers;

use backend\modules\bot\models\Bot;
use backend\modules\bot\src\ApiNp;
use backend\modules\bot\src\DocumentNp;
use backend\modules\bot\telegram\TAdmin;
use backend\modules\customer\models\Customer;
use backend\modules\customer\models\Newsletter;
use backend\modules\shop\models\NoticeNp;
use backend\modules\shop\models\Order;
use Yii;

class ApiHelper
{
    public function getStatusDocuments()
    {
        $order = Order::findOne(6527);
        $apiNp = new ApiNp();


        $documentNp = new DocumentNp($apiNp);
//        \yii\helpers\VarDumper::dump( $order->np->documentData['IntDocNumber'], 1000, 5);die;
        $phone[] = [
            'DocumentNumber' => $order->np->documentData['IntDocNumber'],
            'Phone' => $order->customer->phone
        ];
        $statusDocuments = $documentNp->getStatusDocuments($phone);
        return $statusDocuments;
    }

    public function newsletter()
    {
        $newsletter = Newsletter::find()
            ->where(['id' => 5])
            ->limit(1)->one();
        $customer = Customer::findOne(2029);
        if ($customer->bot->platform == Bot::TELEGRAM) {
            Yii::$app->tm->customer = $customer;
            Yii::$app->tm->platformId = $customer->platform_id;
            $TAdmin = new TAdmin();
            $TAdmin->newsletter($newsletter);
        }
    }

    public function noticeNp()
    {
        $noticeNp = NoticeNp::find()
            ->where(['id' => 18])
            ->limit(1)->one();
        $customer = Customer::findOne(2029);
        Yii::$app->tm->customer = $customer;
        Yii::$app->tm->platformId = $customer->platform_id;
        $TAdmin = new TAdmin();
        $TAdmin->noticeNp($noticeNp, $customer);
    }

}
