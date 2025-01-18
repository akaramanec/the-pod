<?php

namespace console\controllers;

use backend\modules\shop\models\NoticeNpOrderLink;
use backend\modules\shop\models\Order;
use yii\console\Controller;

class CronController extends Controller
{
    public function actionCheckOrdersStatuses()
    {
        $issuedNpOrders = NoticeNpOrderLink::findIssued();
        foreach ($issuedNpOrders as $issuedNpOrder) {
            if ($issuedNpOrder->order->status != Order::STATUS_CLOSE_SUCCESS) {
                $issuedNpOrder->order->status = Order::STATUS_CLOSE_SUCCESS;
                $issuedNpOrder->order->detachBehaviors();
                $issuedNpOrder->order->save();
            }
        }
    }
}
