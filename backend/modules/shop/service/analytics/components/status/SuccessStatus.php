<?php

namespace backend\modules\shop\service\analytics\components\status;

use backend\modules\shop\models\Order;

class SuccessStatus extends AnalyticsStatusItemModel
{

    public function init()
    {
        $this->setStatus(Order::STATUS_CLOSE_SUCCESS);
        $this->setTitle('Выполненые заказы');
    }
}