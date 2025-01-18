<?php

namespace backend\modules\shop\service\analytics\components\status;

use backend\modules\shop\models\Order;

class CanceledStatus extends AnalyticsStatusItemModel
{

    public function init()
    {
        $this->setStatus(Order::STATUS_CLOSE_CANCELED);
        $this->setTitle('Отмененные заказы');
    }
}