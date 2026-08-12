<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Waktu Absensi
    |--------------------------------------------------------------------------
    |
    | Konfigurasi waktu absensi untuk scan masuk dan scan keluar.
    | Anda dapat menyesuaikan waktu sesuai kebutuhan.
    |
    */

    'scan' => [
        'masuk' => [
            'mulai' => '06:00',
            'sampai' => '07:30',
        ],
        'pulang' => [
            'mulai' => '16:30',
            'sampai' => '18:00',
        ],
        'jumat' => [
            'masuk' => [
                'mulai' => '06:00',
                'sampai' => '07:30',
            ],
            'pulang' => [
                'mulai' => '11:30',
                'sampai' => '14:30',
            ],
        ],
    ],
];