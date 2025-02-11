<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'k012GRfDp-ntZbVoUFtFlQ6z00l3b7XN',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Правила для бекенду (за потребою)
                'conversions/<alias:\w+>' => 'conversions/start',
            ],
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
    ];
}

if (YII_ENV_DEV) {
    // Додаємо Gii до bootstrap
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // Додай тут IP-адреси, з яких дозволено доступ до Gii.
        //// Наприклад, якщо твоя IP-адреса 192.168.1.100, то:
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.1.51'],
        // Або, для тимчасового доступу з будь-якого IP (не рекомендовано для продуктивного середовища):
       //'allowedIPs' => ['*'],
    ];
}


return $config;
