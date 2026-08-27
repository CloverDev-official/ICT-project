<?php

return [
    'path' => 'scalar',

    'middleware' => [
        'web',
        'auth:web',
        'access:riwayat-murid',
    ],

    'file' => base_path('Modules/Laporan/openapi/student-attendance.json'),

    'configuration' => [
        'theme' => 'default',
        'layout' => 'modern',
        'defaultHttpClient' => [
            'targetKey' => 'js',
            'clientKey' => 'fetch',
        ],
        'hideModels' => false,
        'hideDownloadButton' => false,
        'persistAuth' => false,
        'metaData' => [
            'title' => 'API Riwayat Absensi Murid',
        ],
    ],
];
