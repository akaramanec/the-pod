<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'home/index',
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'tm' => [
            'class' => 'backend\modules\bot\telegram\TelegramInit',
        ],
        'vb' => [
            'class' => 'backend\modules\bot\viber\ViberInit',
        ],
        'site' => [
            'class' => 'src\components\Site',
        ],
        'common' => [
            'class' => 'src\components\Common',
        ],
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'backend/modules/admin/models/Admin',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'site-pod',
            'timeout' => 864000,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'error/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                 '/' => 'home/index',
                 'catalog' => 'catalog/index',
                 'catalog/<slug:[\w\-]+>' => 'catalog/index',
                 'search' => 'catalog/search',
                 '<action:(about|contact|production|faq|privacy-policy|return-and-exchange-rules)>' => 'home/<action>',
                 'product/<slug:[\w\-]+>' => 'product/index',

//                '/' => 'home/maintenance-mode',
//                'catalog' => 'home/maintenance-mode',
//                'catalog/<slug:[\w\-]+>' => 'home/maintenance-mode',
//                'search' => 'home/maintenance-mode',
//                '<action:(about|contact|production|faq|privacy-policy|return-and-exchange-rules)>' => 'home/maintenance-mode',
//                'product/<slug:[\w\-]+>' => 'home/maintenance-mode',

                'order/np-bot/<order_id:[\w\-]+>' => 'order/np-bot',
                'referral-vb/<ref:[\w\-]+>' => 'home/referral-vb',
                'link-bot/<name:[\w\-]+>' => 'home/link-bot',
                'order' => 'order/index',
                'pay/<id:[\w\-]+>' => 'success/pay',
                'interkassa/<order_id:[\w\-]+>' => 'interkassa/index',
                'telegram' => 'interkassa/telegram',
                'viber' => 'interkassa/viber',
                'pay-callback/' => 'interkassa/pay-callback',
                'success/<ik_pm_no:[\w\-]+>' => 'success/index',
                'return/<id:[\w\-]+>' => 'success/return',
                'fast-order/<id:[\w\-]+>/<qty:[\w\-]+>' => 'cart/fast-order',
            ],
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'bundles' => [
                'yii\bootstrap4\BootstrapAsset' => [
                    'basePath' => '@webroot',
                    'baseUrl' => '@web',
                    'css' => [
                        'assets/css/bootstrap.min.css'
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];
