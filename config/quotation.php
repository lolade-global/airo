<?php

return [
    'fixed_rate' => env('FIXED_RATE') ?: 3, //This is to fallback to 3 even if the env is set to empty

    'age_load_table' => [
        [18, 30, 0.6],
        [31, 40, 0.7],
        [41, 50, 0.8],
        [51, 60, 0.9],
        [61, 70, 1.0],
    ],
];
