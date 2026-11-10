<?php

return [
    'table_prefix' => env('WISHLIST_TABLE_PREFIX', 'itmosoft_'),
    'guests' => [
        'enabled' => true,
        'session_key' => 'itmosoft_wishlist_token',
    ],
    'default_name' => 'My Wishlist',
];
