<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверка на подозрительные запросы
        $this->detectSuspiciousActivity($request);

        // Rate limiting для защиты от DDoS
        $this->rateLimiting($request);

        // Фильтрация вредоносных символов в URL
        $this->filterMaliciousInput($request);

        // Блокировка известных вредоносных IP
        $this->blockMaliciousIPs($request);

        $response = $next($request);

        // Удаление информации о сервере из заголовков
        $this->sanitizeHeaders($response);

        return $response;
    }

    /**
     * Обнаружение подозрительной активности
     */
    private function detectSuspiciousActivity(Request $request): void
    {
        $suspicious_patterns = [
            '/\.\./i',                    // Directory traversal
            '/<script/i',                 // XSS attempts
            '/union.*select/i',           // SQL injection
            '/javascript:/i',             // JavaScript injection
            '/vbscript:/i',              // VBScript injection
            '/onload=/i',                // Event handler injection
            '/onerror=/i',               // Error handler injection
            '/%3Cscript/i',              // Encoded script tags
            '/\<iframe/i',               // Iframe injection
            '/eval\(/i',                 // Eval function
            '/base64_decode/i',          // Base64 decode attempts
            '/system\(/i',               // System command attempts
            '/exec\(/i',                 // Exec function attempts
            '/shell_exec/i',             // Shell execution
            '/phpinfo/i',                // PHP info disclosure
        ];

        $full_url = $request->fullUrl();
        $user_agent = $request->userAgent();
        $ip = $request->ip();

        foreach ($suspicious_patterns as $pattern) {
            if (preg_match($pattern, $full_url) || preg_match($pattern, $user_agent)) {
                Log::warning('Suspicious activity detected', [
                    'ip' => $ip,
                    'url' => $full_url,
                    'user_agent' => $user_agent,
                    'pattern' => $pattern,
                    'timestamp' => now()
                ]);

                // Увеличиваем счетчик подозрительной активности для IP
                $key = 'suspicious_activity_' . $ip;
                $count = Cache::get($key, 0);
                Cache::put($key, $count + 1, now()->addHours(24));

                // Блокируем IP после 5 подозрительных запросов
                if ($count >= 5) {
                    Cache::put('blocked_ip_' . $ip, true, now()->addDays(7));
                    abort(403, 'Access denied due to suspicious activity');
                }

                abort(400, 'Malicious request detected');
            }
        }
    }

    /**
     * Rate limiting для защиты от DDoS
     */
    private function rateLimiting(Request $request): void
    {
        $ip = $request->ip();
        $key = 'rate_limit_' . $ip;

        $requests = Cache::get($key, 0);

        // Увеличиваем лимит до 1000 запросов в минуту с одного IP (более разумно)
        if ($requests >= 1000) {
            Log::warning('Rate limit exceeded', ['ip' => $ip]);
            abort(429, 'Too Many Requests');
        }

        Cache::put($key, $requests + 1, now()->addMinute());
    }

    /**
     * Фильтрация вредоносного ввода
     */
    private function filterMaliciousInput(Request $request): void
    {
        $dangerous_chars = ['<', '>', '"', "'", '&', '%00', '%27', '%22'];

        foreach ($request->all() as $key => $value) {
            if (is_string($value)) {
                foreach ($dangerous_chars as $char) {
                    if (strpos($value, $char) !== false && !$this->isAllowedContext($key)) {
                        Log::warning('Malicious input detected', [
                            'ip' => $request->ip(),
                            'field' => $key,
                            'value' => substr($value, 0, 100),
                            'char' => $char
                        ]);
                        abort(400, 'Invalid input detected');
                    }
                }
            }
        }
    }

    /**
     * Проверка разрешенного контекста для специальных символов
     */
    private function isAllowedContext(string $field): bool
    {
        $allowed_fields = [
            'content',
            'description',
            'message',
            'body',
            'excerpt',           // For news excerpts
            'title',             // For news titles that might contain quotes
            'meta_title',        // For SEO meta titles
            'meta_description',  // For SEO meta descriptions
            'author',            // For author names that might contain apostrophes
            'category',          // For categories that might contain ampersands
            'short_description', // For portfolio short descriptions
            'technologies',      // For portfolio technologies
            'client',            // For portfolio client names
            'bio',               // For team member bios
            'about',             // For about sections
            'summary',           // For summaries
            'details',           // For details
            'note',              // For notes
            'is_published',      // For toggle published status
            'is_featured',       // For toggle featured status
            'is_active',         // For toggle active status
            'status',            // For status changes
            'published_at',      // For publication dates
            'name',              // For names
            'email',             // For email fields
            'phone',             // For phone numbers
            'address',           // For addresses
            'url',               // For URLs
            'image_url',         // For image URLs
            'tags',              // For tags
            'slug',              // For slugs
            // Team member specific fields
            'position',          // For team member positions (might contain & in "Research & Development")
            'department',        // For departments (might contain & in "Sales & Marketing")
            'skills',            // For skills array (might contain quotes in skill names)
            'social_links',      // For social links (URLs contain special characters)
            'avatar',            // For avatar URLs
            'joined_date',       // For join dates
            'priority',          // For priority values
            'show_on_website',   // For visibility toggle
            // Career specific fields
            'employment_type',   // For employment types
            'experience_level',  // For experience levels
            'location',          // For job locations
            'salary_range',      // For salary ranges (might contain $ and commas)
            'remote_available',  // For remote work availability
            'application_deadline', // For application deadlines
            'requirements',      // For job requirements
            'benefits',          // For job benefits
            // Contact form fields
            'subject',           // For contact form subjects
            'company',           // For company names
            'position_interest', // For position of interest
            'resume',            // For resume file names
            // Portfolio specific fields
            'project_url',       // For project URLs
            'github_url',        // For GitHub URLs
            'demo_url',          // For demo URLs
            'features',          // For feature lists
            'challenges',        // For project challenges
            'solutions',         // For project solutions
            // Company info fields
            'company_name',      // For company name
            'company_description', // For company description
            'mission',           // For company mission
            'vision',            // For company vision
            'values',            // For company values
            'history',           // For company history
            'contact_email',     // For contact emails
            'contact_phone',     // For contact phones
            'contact_address',   // For contact addresses
            'social_facebook',   // For Facebook URLs
            'social_twitter',    // For Twitter URLs
            'social_linkedin',   // For LinkedIn URLs
            'social_instagram',  // For Instagram URLs
            'social_youtube',    // For YouTube URLs
            'social_github',     // For GitHub URLs
        ];
        return in_array($field, $allowed_fields);
    }

    /**
     * Блокировка известных вредоносных IP
     */
    private function blockMaliciousIPs(Request $request): void
    {
        $ip = $request->ip();

        // Проверяем, заблокирован ли IP
        if (Cache::has('blocked_ip_' . $ip)) {
            Log::info('Blocked IP attempted access', ['ip' => $ip]);
            abort(403, 'Access denied');
        }

        // Список заблокированных IP-адресов (можно вынести в базу данных)
        $blocked_ips = [
            // Добавьте сюда известные вредоносные IP
        ];

        if (in_array($ip, $blocked_ips)) {
            Cache::put('blocked_ip_' . $ip, true, now()->addDays(30));
            Log::warning('Known malicious IP blocked', ['ip' => $ip]);
            abort(403, 'Access denied');
        }
    }

    /**
     * Очистка заголовков ответа
     */
    private function sanitizeHeaders(Response $response): void
    {
        $response->headers->remove('Server');
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('X-AspNet-Version');
        $response->headers->remove('X-AspNetMvc-Version');
    }
}
