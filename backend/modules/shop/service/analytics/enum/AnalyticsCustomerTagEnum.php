<?php

namespace backend\modules\shop\service\analytics\enum;

class AnalyticsCustomerTagEnum
{
    CONST ONE_ORDER_TAG = 'one-order';
    CONST TO_FIVE_ORDER_TAG = 'to-five-order';
    CONST MORE_FIVE_ORDER_TAG = 'mo-five-order';

    /**
     * @return string[]
     */
    public static function getList(): array
    {
        return [
            self::ONE_ORDER_TAG,
            self::TO_FIVE_ORDER_TAG,
            self::MORE_FIVE_ORDER_TAG
        ];
    }
}