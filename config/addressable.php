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
];
