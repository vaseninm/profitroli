<?php
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'vEtKEawNIItWYuYZPvCbPJdKlZs08S27',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableSession' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
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
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                'debug/<controller>/<action>' => 'debug/<controller>/<action>',

                'POST /invites' => 'invite/create',

                'POST /users' => 'user/registration',
                'POST /users/login' => 'user/login',
                'GET /users/me' => 'user/me',
                'POST /users/upload-avatar' => 'user/upload-avatar',
                'PUT /users/<id:\d+>' => 'user/edit',
                'GET /users/<id:\d+>' => 'user/view',
                'GET /users' => 'user/list',
                'OPTIONS /users' => 'post/options',
                'OPTIONS /users/me' => 'post/options',
                'OPTIONS /users/<id:\d+>' => 'post/options',
                'OPTIONS /users/login' => 'post/options',
                'OPTIONS /users/upload-avatar' => 'post/options',

                'POST /posts' => 'post/create',
                'PUT /posts/<id:\d+>' => 'post/edit',
                'GET /posts' => 'post/list',
                'GET /posts/<id:\d+>' => 'post/view',
                'OPTIONS /posts' => 'post/options',
                'OPTIONS /posts/<id:\d+>' => 'post/options',

                'POST /posts/<post:\d+>/comments' => 'comments/create',
                'GET /posts/<post:\d+>/comments' => 'comments/list',
                'OPTIONS /posts/<post:\d+>/comments' => 'comments/options',
            ]
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
