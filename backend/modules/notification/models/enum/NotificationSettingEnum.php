<?php

namespace backend\modules\notification\models\enum;

class NotificationSettingEnum
{
    const NOT_ACTIVE_TIME = 1;

    public static function getList()
    {
        return [
            self::NOT_ACTIVE_TIME => 'not_active_time'
        ];
    }

}