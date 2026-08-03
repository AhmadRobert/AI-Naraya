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

    /*
    |--------------------------------------------------------------------------
    | Grok (xAI) - PROVIDER UTAMA
    |--------------------------------------------------------------------------
    |
    | model        -> teks (Caption & Storyboard), lihat GrokService.
    |                 Model chat terkini menurut docs.x.ai: grok-4.5.
    | image_model  -> Gabungkan Foto/Edit Foto/Produk Artist/render scene
    |                 Carousel, lihat GrokImageService::generate().
    |                 grok-imagine-image-quality (kualitas tinggi, dipakai
    |                 di Quick Start resmi) atau grok-imagine-image (lebih
    |                 cepat/hemat).
    | file_model   -> BARU: khusus baca PDF storyboard lewat Files API +
    |                 Responses API, lihat GrokImageService::analyzeScenes().
    |                 Sebelumnya key ini belum ada di sini sehingga
    |                 GROK_FILE_MODEL di .env tidak pernah benar-benar
    |                 kebaca (selalu jatuh ke fallback hardcode di kode).
    |
    */
    'grok' => [
        'api_key' => env('GROK_API_KEY'),
        'model' => env('GROK_MODEL', 'grok-4.5'),
        'image_model' => env('GROK_IMAGE_MODEL', 'grok-imagine-image-quality'),
        'file_model' => env('GROK_FILE_MODEL', 'grok-4.3'),
    ],

    /*
    |--------------------------------------------------------------------------
    | ComfyUI Cloud - Gabungkan Foto, Edit Foto
    |--------------------------------------------------------------------------
    | BELUM AKTIF - lihat catatan di app/Services/Providers/ComfyImageService.php.
    | Butuh workflow_api.json yang diekspor dari canvas ComfyUI sebelum
    | fitur ini benar-benar bisa jalan.
    */
    'comfy' => [
        'api_key' => env('COMFY_API_KEY'),
    ],

];