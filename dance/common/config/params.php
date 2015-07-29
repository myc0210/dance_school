<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 7200,
    'user.accessTokenExpire' => 86400,
    'paypalReturn' => [
        'dancepointe' => [
            'paypalReturnSuccess' => 'http://localhost/school_cart/app/checkout/processing',
            'paypalReturnCancel' => 'http://localhost/school_cart/app/checkout/cancel'
        ],
        'dancefactory' => [
            'paypalReturnSuccess' => 'http://localhost/school_cart/app/checkout/processing',
            'paypalReturnCancel' => 'http://localhost/school_cart/app/checkout/cancel'
        ],
        'dacademy' => [
            'paypalReturnSuccess' => 'http://localhost/school_cart/app/checkout/processing',
            'paypalReturnCancel' => 'http://localhost/school_cart/app/checkout/cancel'
        ]
    ]
];
