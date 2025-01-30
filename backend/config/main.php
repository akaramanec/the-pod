<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);
return [
    'id' => 'bot',
    'basePath' => dirname(__DIR__),
    'language' => 'uk',
    'timezone' => 'Europe/Kyiv',
    'sourceLanguage' => 'uk-UA',
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute' => 'customer/statistic/index',
    'bootstrap' => ['log'],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'tm' => [
            'class' => 'backend\modules\bot\telegram\TelegramInit',
        ],
        'vb' => [
            'class' => 'backend\modules\bot\viber\ViberInit',
        ],
        'common' => [
            'class' => 'src\components\Common',
        ],
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/lang',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ]
            ],
        ],
        'user' => [
            'identityClass' => 'backend\modules\admin\models\Admin',
            'enableAutoLogin' => true,
            'enableSession' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'loginUrl' => ['admin/login/login'],
        ],
        'session' => [
            'name' => 'advanced-backend',
            'timeout' => 2592000,
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
                '/' => 'customer/statistic/index',
                'login' => 'admin/login/login',
                'admin' => 'admin/admin/index',
                'logout' => 'admin/login/logout',
            ],
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'bundles' => [
                'kartik\form\ActiveFormAsset' => [
                    'bsDependencyEnabled' => false // do not load bootstrap assets for a specific asset bundle
                ],
                'yii\bootstrap4\BootstrapAsset' => [
                    'basePath' => '@webroot',
                    'baseUrl' => '@web',
                    'css' => [
                        'css/bootstrap.min.css',
                    ],
                ],
            ],
        ],
    ],
    'modules' => [
        'admin' => [
            'class' => 'backend\modules\admin\Module',
        ],
        'shop' => [
            'class' => 'backend\modules\shop\Module',
        ],
        'customer' => [
            'class' => 'backend\modules\customer\Module',
        ],
        'media' => [
            'class' => 'backend\modules\media\Module',
        ],
        'bot' => [
            'class' => 'backend\modules\bot\Module',
        ],
        'system' => [
            'class' => 'backend\modules\system\Module',
        ],
        'jodit' => [
            'class' => 'yii2jodit\JoditModule',
            'extensions' => ['jpg', 'png', 'gif'],
            'root' => '@backend/web/uploads/jodit/',
            'baseurl' => $params['dataUrl'] . '/uploads/jodit/',
            'maxFileSize' => '20mb',
            'defaultPermission' => 0775,
        ],
        'notification' => [
            'class' => 'backend\modules\notification\Module',
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module'
        ]
    ],
    'as access' => [
        'class' => 'yii\filters\AccessControl',
        'except' => [
            'admin/login/reset-password',
            'admin/login/login',
            'admin/login/logout',
            'admin/login/request-password-reset',
            'error/check-relevance',
            'admin/login/error',
            'bot/hook/telegram',
            'bot/hook/viber',
            'shop/order/index',
            'shop/order/update',
            'customer/customer/index',
            'customer/customer/update',
            'customer/customer/view',
            'shop/order-poll/index',
            '/shop/poll/index',
            '/shop/poll/create',
            '/shop/poll/update'
        ],
        'rules' => [
            [
                'allow' => true,
                'roles' => ['dev', 'admin'],
            ],
        ],
    ],
    'params' => $params,
];
