<?php

namespace backend\modules\shop\service\analytics\components\status;

use backend\modules\shop\models\Order;

class ConfirmedViewedStatus extends AnalyticsStatusItemModel
{

    public function init()
    {
        $this->setStatus(Order::STATUS_CONFIRMED_AND_VIEWED);
        $this->setTitle('Подтверждён');
    }
}