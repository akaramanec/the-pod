<?php

namespace backend\modules\shop\service\analytics\components\status;

use backend\modules\shop\models\Order;

class InProcessingStatus extends AnalyticsStatusItemModel
{

    public function init()
    {
        $this->setStatus(Order::STATUS_IN_PROCESSING);
        $this->setTitle('В обработкe');
    }
}