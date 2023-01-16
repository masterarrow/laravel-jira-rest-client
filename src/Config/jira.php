<?php

return [
    'host' => env('JIRA_HOST'),

    'default_auth' => env('JIRA_AUTHENTICATION', 'basic'),

    'auth' => [
        'basic' => [
            'username' => env('JIRA_USER'),
            'password' => env('JIRA_PASS'),

            'middleware' => \Atlassian\JiraRest\Requests\Middleware\BasicAuthMiddleware::class,
        ],

        'oauth2' => [
            'oauth_token' => env('JIRA_OAUTH_TOKEN', ''),
            'oauth_token_secret' => env('JIRA_OAUTH_TOKEN_SECRET', ''),
            'impersonate' => env('JIRA_IMPERSONATE', false),

            'routes' => env('JIRA_OAUTH_ROUTES', false),

            // 'middleware' => \Atlassian\JiraRest\Requests\Middleware\OAuthMiddleware::class,
        ],

        'basic_token' => [
            'username' => env('JIRA_USER'),
            'token' => env('JIRA_API_TOKEN'),

            'middleware' => \Atlassian\JiraRest\Requests\Middleware\BasicApiTokenMiddleware::class,
        ],
    ],

    'log_level' => env('JIRA_LOG_LEVEL', 'WARNING'),

    'client_options' => [

    ],

    'session' => [
        'name' => 'jira_session',
        'duration' => 3600,
    ],
];
