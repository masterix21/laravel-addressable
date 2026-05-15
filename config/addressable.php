<?php

return [
    'models' => [
        /**
         * If you like to use a custom Address model, change it.
         * For example, its useful if you like to use UUID instead
         * of integer ids.
         */
        'address' => \Masterix21\Addressable\Models\Address::class,
    ],

    'tables' => [
        /**
         * If you like to customize the table name, change it.
         * It must be changed before of migration command.
         */
        'addresses' => 'addresses',
    ],

    'srid' => 4326,

    /**
     * Format for the display_address accessor.
     * Use {field_name} placeholders. Set to null to use the default format.
     * Example: '{street_address1}, {street_address2}, {zip} {city}, {state}, {country}'
     */
    'display_format' => null,

    /**
     * Geocoding turns a textual address into coordinates (and back, via reverse).
     * Drivers are tried in order (FIFO): the first one returning a result wins.
     * Remove a driver entry to disable it. Each driver gets its own config block.
     */
    'geocoding' => [
        'user_agent' => env('ADDRESSABLE_GEOCODER_UA', 'laravel-addressable'),

        /**
         * When true, addresses without coordinates are geocoded automatically
         * on save. Off by default to avoid unexpected network calls.
         */
        'auto' => env('ADDRESSABLE_GEOCODER_AUTO', false),

        'drivers' => [
            'nominatim' => [
                'class' => \Masterix21\Addressable\Geocoding\Drivers\NominatimGeocoder::class,
                'endpoint' => 'https://nominatim.openstreetmap.org/search',
                'reverse_endpoint' => 'https://nominatim.openstreetmap.org/reverse',
            ],

            'photon' => [
                'class' => \Masterix21\Addressable\Geocoding\Drivers\PhotonGeocoder::class,
                'endpoint' => 'https://photon.komoot.io/api',
                'reverse_endpoint' => 'https://photon.komoot.io/reverse',
            ],

            // 'google' => [
            //     'class' => \Masterix21\Addressable\Geocoding\Drivers\GoogleGeocoder::class,
            //     'endpoint' => 'https://maps.googleapis.com/maps/api/geocode/json',
            //     'api_key' => env('GOOGLE_GEOCODER_KEY'),
            // ],
        ],
    ],
];
