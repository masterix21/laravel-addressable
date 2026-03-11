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
];
