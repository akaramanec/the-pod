<?php

namespace backend\modules\shop\service\analytics\enum;

class AnalyticsNewsletterIndicatorTypeEnum
{
    const ONE = 'one';
    const TO_FIVE = 'toFive';
    const MORE_FIVE = 'moreFive';

    /**
     * @return string[]
     */
    public static function getList(): array
    {
        return [
            self::ONE,
            self::TO_FIVE,
            self::MORE_FIVE
        ];
    }
}