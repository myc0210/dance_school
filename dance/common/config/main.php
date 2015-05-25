<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
            'itemFile' => '@backend/rbac/items.php', //Default path to items.php | NEW
            // CONFIGURATIONS
            'assignmentFile' => '@backend/rbac/assignments.php', //Default path to assignments.php | NEW CONFIGURATIONS
            'ruleFile' => '@backend/rbac/rules.php', //Default path to rules.php | NEW CONFIGURATIONS
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'template' => 'template/index',
            ],
        ],
        'flysystem' => [
            'class' => 'creocoder\flysystem\LocalFilesystem',
            'path' => '@webroot/images',
        ],
    ],
    'aliases' => [
        '@upload' => 'images',
    ],
];
