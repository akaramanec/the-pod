<?php

namespace backend\modules\shop\service\analytics\components\newsletter;



use backend\modules\shop\service\analytics\enum\AnalyticsCustomerTagEnum;
use yii\helpers\Url;

class ToFiveOrder extends AnalyticsNewsletterItemModel
{

    public function init()
    {
        $this->setTitle('2-5 заказов');
        $this->setGetParams('tag', AnalyticsCustomerTagEnum::TO_FIVE_ORDER_TAG);
    }
}