<?php

return [
    'face_detection' => [
        'min_detection_confidence' => env('MEDIAPIPE_MIN_CONFIDENCE', 0.5),
        'model_selection' => env('MEDIAPIPE_MODEL', 0), // 0: short-range, 1: full-range
    ],

    'face_mesh' => [
        'max_num_faces' => 1,
        'min_detection_confidence' => 0.5,
        'min_tracking_confidence' => 0.5,
    ],

    'image' => [
        'max_size' => env('FACE_IMAGE_MAX_SIZE', 5120), // 5MB in KB
        'allowed_formats' => ['jpg', 'jpeg', 'png'],
        'storage_path' => 'analisis-facial',
    ],

    'processing' => [
        'timeout' => env('FACE_PROCESSING_TIMEOUT', 10), // seconds
        'queue' => env('FACE_QUEUE_ENABLED', true),
        'queue_name' => 'face-analysis',
    ],
];
