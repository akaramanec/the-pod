<?php

namespace backend\modules\shop\service\analytics\enum;

class AnalyticsGeneralTypeEnum
{
    const AVERAGE_COUNT_ORDER = 'averageCountOrder';
    const LVT_ORDER = 'lvtOrder';
    const AVERAGE_SUM_ORDER = 'averageSumOrder';
    const MAX_SUM_ORDER = 'maxSumOrder';
    const SUCCESS_ORDER = 'successOrder';
    const SUM_SUCCESS_ORDER = 'sumSuccessOrder';
    const USERS_ALL = 'usersAll';
    const USERS_UNIQUE = 'usersUnique';
    const USERS_UNSUBSCRIBED = 'usersUnsubscribed';

    /**
     * @return string[]
     */
    public static function getList(): array
    {
        return [
            self::AVERAGE_COUNT_ORDER,
            self::AVERAGE_SUM_ORDER,
            self::MAX_SUM_ORDER,
            self::SUCCESS_ORDER,
            self::SUM_SUCCESS_ORDER,
            self::USERS_ALL,
            self::USERS_UNIQUE,
            self::USERS_UNSUBSCRIBED
        ];
    }
}