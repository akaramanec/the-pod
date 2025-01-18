<?php

namespace backend\modules\shop\service\analytics\components\newsletter;


use backend\modules\shop\service\analytics\enum\AnalyticsCustomerTagEnum;
use yii\helpers\Url;

class OneOrder extends AnalyticsNewsletterItemModel
{

    public function init()
    {
        $this->setTitle('1 заказ');
        $this->setGetParams('tag', AnalyticsCustomerTagEnum::ONE_ORDER_TAG);
    }
}