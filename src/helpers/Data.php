<?php

namespace src\helpers;


use Yii;

class Data
{

    public static function active_cabinet()
    {
        return [
            'query/location', 'response/location',
            'query/casting', 'response/casting',
            'query/camera-crew', 'response/camera-crew',
            'query/post-production', 'response/post-production',
            'query/transport', 'response/transport',
            'query/production', 'response/production',
            'query/art', 'response/art',
            'query/beauty-style', 'response/beauty-style',
            'query/suit-rent', 'response/suit-rent',
            'query/catering', 'response/catering',
            'query/iron-rent', 'response/iron-rent',
            'query/consulting', 'response/consulting',
            'query/full-service', 'response/full-service',
            'cabinet/index','cabinet/update',
        ];
    }

    public static function active_loc()
    {
        return [
            'location/index', 'location/create', 'location/update',
        ];
    }

    public static function active_service()
    {
        return [
            'attribute-value/index', 'attribute-value/create', 'attribute-value/update',
            'casting/index', 'casting/create', 'casting/update', 'casting/view',
            'location/index', 'location/create', 'location/update', 'location/view',
            'transport/index', 'transport/create', 'transport/update', 'transport/view',
            'camera-crew/index', 'camera-crew/create', 'camera-crew/update', 'camera-crew/view',
        ];
    }

    public static function active_common()
    {
        return [
            'driver-license/index', 'driver-license/create', 'driver-license/update',
            'language/index', 'language/create', 'language/update',
            'nationality/index', 'nationality/create', 'nationality/update',
            'visa/index', 'visa/create', 'visa/update',
        ];

    }

    public static function entities()
    {
        return [
            LOCATION => 'Локації',
            CASTING => 'Кастинг',
            CAMERA_CREW => 'Операторський Склад',
            POST_PRODUCTION => 'Пост Продакшн',
            TRANSPORT => 'Департамент Транспорту',
            ART => 'Художній Відділ',
            BEAUTY_STYLE => 'Краса Та Стиль',
            SUIT_RENT => 'Оренда Костюмів',
            CATERING => 'Кейтерінг',
            IRON_RENT => 'Оренда Обладнання',
            CONSULTING => 'Консалтинг',
            FULL_SERVICE => 'Повний Сервіс Виробництва',
        ];
    }
}