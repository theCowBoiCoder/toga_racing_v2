<?php

return [

    'instagram' => [
        'user_id' => env('INSTAGRAM_USER_ID'),
        'access_token' => env('INSTAGRAM_ACCESS_TOKEN'),
        'profile_url' => env('INSTAGRAM_PROFILE_URL', 'https://www.instagram.com/toga_racing/'),
        'alternate_posts' => array_filter(explode(',', env('INSTAGRAM_ALTERNATE_POSTS', 'Dai8OYnDFzk,Dai8K2AjDL5'))),
    ],

    'enquiries' => [
        'to' => env('ENQUIRY_NOTIFICATION_EMAIL'),
    ],

    'discord' => [
        'bot_token' => env('DISCORD_BOT_TOKEN'),
        'partner_channel' => env('DISCORD_PARTNER_CHANNEL_ID', '1527599240405323887'),
        'driver_channel' => env('DISCORD_DRIVER_CHANNEL_ID', '1527599269392023633'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

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

];
