<?php

namespace backend\modules\shop\service\analytics\components\status;

use backend\modules\shop\models\Order;

class UnconfirmedStatus extends AnalyticsStatusItemModel
{

    public function init()
    {
        $this->setStatus(Order::STATUS_UNCONFIRMED);
        $this->setTitle('Не подтверждён');
    }
}