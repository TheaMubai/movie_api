<?php

return [
    'cloud' => env('CLOUDINARY_CLOUD_NAME'),
    'key' => env('CLOUDINARY_API_KEY'),
    'secret' => env('CLOUDINARY_API_SECRET'),
    'url' => [
        'secure' => env('CLOUDINARY_SECURE_URL', true), // It's good practice to get this from env too
    ],
];
