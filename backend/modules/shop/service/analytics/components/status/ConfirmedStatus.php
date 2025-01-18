<?php

namespace backend\modules\shop\service\analytics\components\status;

use backend\modules\shop\models\Order;

class ConfirmedStatus extends AnalyticsStatusItemModel
{

    public function init()
    {
        $this->setStatus(Order::STATUS_CONFIRMED);
        $this->setTitle('Подтверждён (новый)');
    }
}