<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8mb4',
            'dsn' => 'mysql:host=nv397414.mysql.tools;dbname=nv397414_bot',
            'username' => 'nv397414_bot',
            'password' => 'X4x4+s6Hm*',
//            'enableSchemaCache' => true,
        ],
        'loggerDb' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8mb4',
            'dsn' => 'mysql:host=nv397414.mysql.tools;dbname=nv397414_logger',
            'username' => 'nv397414_logger',
            'password' => 'H5-F_gh7r2',
//            'enableSchemaCache' => true,
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'viewPath' => '@common/mail',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'thepod.com.ua@gmail.com',
                'password' => 'thepodik',
                'port' => '587',
                'encryption' => 'tls',
            ],
        ],
    ],
];
