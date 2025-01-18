<?php

namespace backend\modules\shop\service\analytics\components\newsletter;


use backend\modules\shop\service\analytics\enum\AnalyticsCustomerTagEnum;
use yii\helpers\Url;

class MoreFiveOrder extends AnalyticsNewsletterItemModel
{

    public function init()
    {
        $this->setTitle('> 5 Заказов');
        $this->setGetParams('tag', AnalyticsCustomerTagEnum::MORE_FIVE_ORDER_TAG);
    }
}