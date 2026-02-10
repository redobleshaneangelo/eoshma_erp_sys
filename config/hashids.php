<?php

return [
    'default' => 'main',

    'connections' => [
        'main' => [
            'salt' => env('HASHIDS_SALT', 'S9uVx!n5ZqTgWm@LjY8bRf3!pH7e2KdA'),
            'length' => 10,
            'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
        ],
    ],
];
