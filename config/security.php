<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Основные настройки безопасности для вашего приложения
    |
    */

    'rate_limiting' => [
        'max_requests_per_minute' => env('SECURITY_RATE_LIMIT', 100),
        'max_requests_per_hour' => env('SECURITY_RATE_LIMIT_HOUR', 1000),
        'block_duration_hours' => env('SECURITY_BLOCK_DURATION', 24),
    ],

    'brute_force_protection' => [
        'max_login_attempts' => env('SECURITY_MAX_LOGIN_ATTEMPTS', 5),
        'lockout_time_minutes' => env('SECURITY_LOCKOUT_TIME', 30),
        'attempt_window_minutes' => env('SECURITY_ATTEMPT_WINDOW', 15),
    ],

    'ip_whitelist' => [
        // Добавьте доверенные IP адреса
        '127.0.0.1',
        '::1',
        // env('ADMIN_IP', ''),
    ],

    'ip_blacklist' => [
        // Постоянно заблокированные IP адреса
        // '192.168.1.100',
    ],

    'content_security_policy' => [
        'report_uri' => env('CSP_REPORT_URI', '/security/csp-report'),
        'report_only' => env('CSP_REPORT_ONLY', false),
    ],

    'file_upload' => [
        'max_file_size' => env('SECURITY_MAX_FILE_SIZE', 10485760), // 10MB
        'allowed_extensions' => [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
            'pdf', 'doc', 'docx', 'txt', 'csv', 'xlsx'
        ],
        'forbidden_extensions' => [
            'php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'bat', 'cmd',
            'com', 'pif', 'scr', 'vbs', 'js', 'jar', 'sh', 'py', 'pl', 'rb'
        ],
        'scan_for_malware' => env('SECURITY_SCAN_UPLOADS', true),
    ],

    'session_security' => [
        'regenerate_on_login' => true,
        'invalidate_on_password_change' => true,
        'max_lifetime_hours' => env('SESSION_MAX_LIFETIME', 8),
    ],

    'logging' => [
        'log_failed_logins' => true,
        'log_suspicious_activity' => true,
        'log_blocked_requests' => true,
        'log_admin_actions' => true,
    ],

    'admin_protection' => [
        'require_2fa' => env('ADMIN_REQUIRE_2FA', false),
        'allowed_ips' => [
            // Ограничить доступ к админ панели по IP
        ],
        'session_timeout_minutes' => env('ADMIN_SESSION_TIMEOUT', 30),
    ],

    'database_security' => [
        'disable_raw_queries_in_production' => true,
        'log_slow_queries' => true,
        'encrypt_sensitive_data' => true,
    ],

    'api_security' => [
        'require_api_key' => env('API_REQUIRE_KEY', true),
        'api_key' => env('API_KEY'),
        'rate_limit_per_minute' => env('API_RATE_LIMIT', 60),
        'allowed_origins' => [
            env('APP_URL'),
            'https://darkheim.net',
            'https://www.darkheim.net',
        ],
    ],
];
