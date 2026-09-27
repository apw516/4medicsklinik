<?php

return [
   'driver' => env('LOCATION_DRIVER', Stevebauman\Location\Drivers\IpApi::class),

    /*
    |--------------------------------------------------------------------------
    | Fallback Drivers
    |--------------------------------------------------------------------------
    |
    | Driver cadangan yang akan dicoba secara berurutan jika driver utama gagal.
    |
    */

    'fallbacks' => [
        'Stevebauman\Location\Drivers\IpInfo',
        'Stevebauman\Location\Drivers\GeoPlugin',
    ],

    /*
    |--------------------------------------------------------------------------
    | Position Class
    |--------------------------------------------------------------------------
    |
    | Class instance yang digunakan untuk menampung hasil respon lokasi.
    |
    */

    'position' => Stevebauman\Location\Position::class,

    /*
    |--------------------------------------------------------------------------
    | Localhost Testing IP
    |--------------------------------------------------------------------------
    |
    | IP fallback saat aplikasi dijalankan di lingkungan lokal (127.0.0.1 / ::1).
    |
    */

    'testing' => [
        'ip' => '180.252.80.1',
        'enabled' => env('LOCATION_TESTING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurations for Drivers
    |--------------------------------------------------------------------------
    */

    'drivers' => [

        'Stevebauman\Location\Drivers\IpApi' => [
            'url' => 'http://ip-api.com/json/',
            'https' => false,
        ],

        'Stevebauman\Location\Drivers\IpInfo' => [
            'token' => env('IPINFO_TOKEN'),
        ],

        'Stevebauman\Location\Drivers\GeoPlugin' => [
            'url' => 'http://www.geoplugin.net/php.gp?ip=',
        ],

        'Stevebauman\Location\Drivers\MaxMind' => [
            'license_key' => env('MAXMIND_LICENSE_KEY'),
            'web' => [
                'enabled' => false,
                'user_id' => env('MAXMIND_USER_ID'),
                'options' => [],
            ],
            'local' => [
                'path' => database_path('location/geoip2.mmdb'),
            ],
        ],

    ],

];