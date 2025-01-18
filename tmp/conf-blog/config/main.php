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
    'controllerNamespace' => 'blog\controllers',
    'defaultRoute' => 'home/index',
    'bootstrap' => ['log'],
    'components' => [
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
            'csrfParam' => '_csrf-blog',
        ],
        'user' => [
            'identityClass' => 'blog\modules\admin\models\AuthBlogger',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-blog', 'httpOnly' => true],
            'loginUrl' => ['admin/login/login'],
        ],
        'session' => [
            'name' => 'advanced-blog',
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
                'login' => 'admin/login/login',
                'admin' => 'admin/admin/index',
                'logout' => 'admin/login/logout',
            ],
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'bundles' => [
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
            'class' => 'blog\modules\admin\Module',
        ],
        'jodit' => [
            'class' => 'yii2jodit\JoditModule',
            'extensions' => ['jpg', 'png', 'gif'],
            'root' => '@backend/web/uploads/jodit/',
            'baseurl' => $params['dataUrl'] . '/uploads/jodit/',
            'maxFileSize' => '20mb',
            'defaultPermission' => 0775,
        ],
    ],
    'as access' => [
        'class' => 'yii\filters\AccessControl',
        'except' => [
            'admin/login/reset-password',
            'admin/login/login',
            'admin/login/request-password-reset',
            'admin/login/error',
        ],
        'rules' => [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'params' => $params,
];
