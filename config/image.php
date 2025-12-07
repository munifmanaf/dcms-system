<?php

return [
    'upload' => [
        'max_size' => 5120, // 5MB in KB
        'allowed_mimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    ],
    
    'sizes' => [
        'thumbnail' => [
            'width' => 150,
            'height' => 150,
            'quality' => 80,
        ],
        'medium' => [
            'width' => 800,
            'height' => 600,
            'quality' => 85,
        ],
        'large' => [
            'width' => 1200,
            'height' => 900,
            'quality' => 90,
        ],
    ],
    
    'watermark' => [
        'enabled' => false,
        'text' => 'DCMS',
        'position' => 'bottom-right',
        'font_size' => 24,
        'color' => [255, 255, 255, 0.6],
    ],
    
    'optimization' => [
        'enabled' => true,
        'quality' => 85,
    ],
    
    'storage' => [
        'disk' => 'public',
        'directory' => 'images',
        'keep_original' => true,
    ],
];