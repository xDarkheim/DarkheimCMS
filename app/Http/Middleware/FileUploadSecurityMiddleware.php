<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FileUploadSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем только запросы с файлами
        if ($request->hasFile('*') || $this->hasFileUpload($request)) {
            $this->validateFileUploads($request);
        }

        return $next($request);
    }

    /**
     * Проверка наличия загрузки файлов
     */
    private function hasFileUpload(Request $request): bool
    {
        return $request->isMethod('POST') && (
            str_contains($request->header('Content-Type', ''), 'multipart/form-data') ||
            str_contains($request->header('Content-Type', ''), 'application/octet-stream')
        );
    }

    /**
     * Валидация загружаемых файлов
     */
    private function validateFileUploads(Request $request): void
    {
        $maxFileSize = config('security.file_upload.max_file_size', 10485760); // 10MB
        $allowedExtensions = config('security.file_upload.allowed_extensions', []);
        $forbiddenExtensions = config('security.file_upload.forbidden_extensions', []);

        foreach ($request->allFiles() as $key => $files) {
            $fileArray = is_array($files) ? $files : [$files];

            foreach ($fileArray as $file) {
                if (!$file->isValid()) {
                    $this->logSecurityEvent('Invalid file upload', $request, $key);
                    abort(400, 'Invalid file upload');
                }

                // Проверка размера файла
                if ($file->getSize() > $maxFileSize) {
                    $this->logSecurityEvent('File too large', $request, $key, [
                        'file_size' => $file->getSize(),
                        'max_size' => $maxFileSize
                    ]);
                    abort(413, 'File too large');
                }

                // Проверка расширения файла
                $extension = strtolower($file->getClientOriginalExtension());

                if (in_array($extension, $forbiddenExtensions)) {
                    $this->logSecurityEvent('Forbidden file extension', $request, $key, [
                        'extension' => $extension,
                        'filename' => $file->getClientOriginalName()
                    ]);
                    abort(415, 'File type not allowed');
                }

                if (!empty($allowedExtensions) && !in_array($extension, $allowedExtensions)) {
                    $this->logSecurityEvent('Extension not in whitelist', $request, $key, [
                        'extension' => $extension,
                        'filename' => $file->getClientOriginalName()
                    ]);
                    abort(415, 'File type not allowed');
                }

                // Проверка MIME типа
                $this->validateMimeType($file, $request, $key);

                // Проверка на вредоносное содержимое
                $this->scanFileContent($file, $request, $key);

                // Проверка заголовков файла
                $this->validateFileHeaders($file, $request, $key);
            }
        }
    }

    /**
     * Валидация MIME типа
     */
    private function validateMimeType(\Illuminate\Http\UploadedFile $file, Request $request, string $key): void
    {
        $allowedMimeTypes = [
            // Изображения
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            // Документы
            'application/pdf', 'text/plain', 'text/csv',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        $mimeType = $file->getMimeType();

        if (!in_array($mimeType, $allowedMimeTypes)) {
            $this->logSecurityEvent('Invalid MIME type', $request, $key, [
                'mime_type' => $mimeType,
                'filename' => $file->getClientOriginalName()
            ]);
            abort(415, 'File MIME type not allowed');
        }
    }

    /**
     * Сканирование содержимого файла на вредоносный код
     */
    private function scanFileContent(\Illuminate\Http\UploadedFile $file, Request $request, string $key): void
    {
        if (!config('security.file_upload.scan_for_malware', true)) {
            return;
        }

        $content = file_get_contents($file->getPathname());

        // Проверка на PHP код в загружаемых файлах
        $phpPatterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<script[^>]*php/i',
            '/eval\s*\(/i',
            '/base64_decode\s*\(/i',
            '/system\s*\(/i',
            '/exec\s*\(/i',
            '/shell_exec\s*\(/i',
            '/passthru\s*\(/i',
            '/file_get_contents\s*\(/i',
            '/fopen\s*\(/i',
            '/curl_exec\s*\(/i'
        ];

        foreach ($phpPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $this->logSecurityEvent('Malicious code detected in file', $request, $key, [
                    'pattern' => $pattern,
                    'filename' => $file->getClientOriginalName()
                ]);
                abort(415, 'Malicious content detected');
            }
        }

        // Проверка на SQL инъекции в текстовых файлах
        if (str_contains($file->getMimeType(), 'text/')) {
            $sqlPatterns = [
                '/union\s+select/i',
                '/drop\s+table/i',
                '/insert\s+into/i',
                '/delete\s+from/i',
                '/update\s+set/i'
            ];

            foreach ($sqlPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    $this->logSecurityEvent('SQL injection attempt in file', $request, $key, [
                        'pattern' => $pattern,
                        'filename' => $file->getClientOriginalName()
                    ]);
                    abort(415, 'Suspicious content detected');
                }
            }
        }
    }

    /**
     * Проверка заголовков файла
     */
    private function validateFileHeaders(\Illuminate\Http\UploadedFile $file, Request $request, string $key): void
    {
        $filename = $file->getClientOriginalName();

        // Проверка на подозрительные имена файлов
        $suspiciousPatterns = [
            '/\.php\./i',
            '/\.asp\./i',
            '/\.jsp\./i',
            '/\.cgi\./i',
            '/\.pl\./i',
            '/\.py\./i',
            '/\.sh\./i',
            '/\.bat\./i',
            '/\.cmd\./i',
            '/\.exe\./i'
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $filename)) {
                $this->logSecurityEvent('Suspicious filename pattern', $request, $key, [
                    'filename' => $filename,
                    'pattern' => $pattern
                ]);
                abort(400, 'Suspicious filename detected');
            }
        }

        // Проверка на null bytes в имени файла
        if (strpos($filename, "\0") !== false || strpos($filename, '%00') !== false) {
            $this->logSecurityEvent('Null byte in filename', $request, $key, [
                'filename' => $filename
            ]);
            abort(400, 'Invalid filename');
        }

        // Проверка длины имени файла
        if (strlen($filename) > 255) {
            $this->logSecurityEvent('Filename too long', $request, $key, [
                'filename_length' => strlen($filename)
            ]);
            abort(400, 'Filename too long');
        }
    }

    /**
     * Логирование событий безопасности
     * @param array<string, mixed> $extra
     */
    private function logSecurityEvent(string $event, Request $request, string $field, array $extra = []): void
    {
        Log::warning("File upload security event: {$event}", array_merge([
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'field' => $field,
            'user_agent' => $request->userAgent(),
            'timestamp' => now()
        ], $extra));
    }
}
