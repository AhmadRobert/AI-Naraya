<?php

return [

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'grok' => [
        'api_key' => env('GROK_API_KEY'),
        'model' => env('GROK_MODEL', 'grok-4.5'),
        'image_model' => env('GROK_IMAGE_MODEL', 'grok-imagine-image'),
        'file_model' => env('GROK_FILE_MODEL', 'grok-4.3'),
    ],

    'comfy' => [
        'api_key' => env('COMFY_CLOUD_API_KEY'),
        'url' => env('COMFY_CLOUD_URL', 'https://cloud.comfy.org'),
    ],

];