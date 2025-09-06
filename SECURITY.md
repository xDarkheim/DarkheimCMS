# Security Configuration Guide

## 🔒 Comprehensive Security Setup for darkheim.net

### Security Features Implemented

#### 1. Security Headers
- **X-Frame-Options**: DENY - Защита от кликджекинга
- **Content-Security-Policy**: Строгая политика безопасности контента с nonce
- **X-Content-Type-Options**: nosniff - Защита от MIME-sniffing
- **Strict-Transport-Security**: HSTS с preload
- **X-XSS-Protection**: Защита от XSS атак
- **Permissions-Policy**: Ограничение доступа к API браузера

#### 2. Attack Protection Middleware
- **SecurityMiddleware**: Обнаружение подозрительной активности и rate limiting
- **SQLInjectionProtectionMiddleware**: Защита от SQL инъекций и XSS
- **BruteForceProtectionMiddleware**: Защита от брутфорс атак
- **AntiCSRFMiddleware**: Защита от CSRF атак
- **FileUploadSecurityMiddleware**: Безопасная загрузка файлов
- **APISecurityMiddleware**: Защита API эндпоинтов

#### 3. Session Security
- Шифрование сессий включено
- Secure cookies только через HTTPS
- HttpOnly cookies
- SameSite=Strict для защиты от CSRF
- Автоматическая регенерация сессий

#### 4. Database Security
- Высокая сложность хеширования (BCRYPT_ROUNDS=12)
- Защищенные пароли в production
- Шифрование чувствительных данных

### Security Management Commands

```bash
# Отчет о безопасности
php artisan security:manage security-report

# Блокировка IP адреса
php artisan security:manage block-ip 192.168.1.100

# Разблокировка IP адреса
php artisan security:manage unblock-ip 192.168.1.100

# Просмотр заблокированных IP/email
php artisan security:manage list-blocked

# Очистка попыток входа
php artisan security:manage clear-attempts
```

### Configuration Files

#### Environment Variables (.env)
```
# Основные настройки
APP_ENV=production
APP_DEBUG=false
APP_URL=https://darkheim.net

# Безопасность сессий
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
SESSION_DOMAIN=darkheim.net

# Защита от брутфорса
SECURITY_MAX_LOGIN_ATTEMPTS=5
SECURITY_LOCKOUT_TIME=30
SECURITY_ATTEMPT_WINDOW=15

# Rate limiting
SECURITY_RATE_LIMIT=100
SECURITY_RATE_LIMIT_HOUR=1000

# API безопасность
API_REQUIRE_KEY=true
API_RATE_LIMIT=60
```

### Security Monitoring

#### Log Files
Все события безопасности логируются в:
- `storage/logs/laravel.log`
- Подозрительная активность
- Неудачные попытки входа
- Заблокированные запросы
- Вредоносные загрузки файлов

#### Rate Limiting
- **Web**: 100 запросов в минуту с IP
- **API**: 60 запросов в минуту с API ключом
- **Login**: 5 попыток в 15 минут

#### Automatic Blocking
- SQL injection попытки: Мгновенная блокировка
- Множественные подозрительные запросы: Блокировка на 24 часа
- Брутфорс атаки: Блокировка на 30 минут
- Вредоносные загрузки: Мгновенная блокировка

### File Upload Security

#### Allowed Extensions
- Images: jpg, jpeg, png, gif, webp, svg
- Documents: pdf, doc, docx, txt, csv, xlsx

#### Security Checks
- MIME type validation
- File size limits (10MB max)
- Malware scanning
- PHP code detection
- Filename validation

### Content Security Policy

Current CSP includes:
- `default-src 'self'`
- `script-src` with nonce-based execution
- `style-src` with nonce support
- `object-src 'none'`
- `base-uri 'self'`
- `frame-ancestors 'none'`

### API Security

#### Requirements
- API key validation
- CORS policy enforcement
- JSON payload size limits
- Malicious content detection
- Rate limiting per API key

### Monitoring Dashboard

The security system provides:
- Real-time threat detection
- Automatic IP blocking
- Security event logging
- Performance monitoring
- Configuration validation

### Recommendations

1. **Regular Updates**: Keep Laravel and dependencies updated
2. **SSL/TLS**: Ensure valid SSL certificate is active
3. **Firewall**: Configure server-level firewall rules
4. **Backup**: Regular security-aware backups
5. **Monitoring**: Review security logs regularly

### Emergency Procedures

#### If Under Attack
```bash
# Block specific IP immediately
php artisan security:manage block-ip [ATTACK_IP]

# Clear all login attempts
php artisan security:manage clear-attempts

# Review recent logs
tail -f storage/logs/laravel.log | grep -i "security\|warning\|critical"
```

#### Recovery
```bash
# Unblock legitimate IP
php artisan security:manage unblock-ip [LEGITIMATE_IP]

# Clear cache if needed
php artisan cache:clear

# Restart application if necessary
php artisan config:cache
php artisan route:cache
```

### Security Status: ✅ ACTIVE

All security components are installed and configured:
- ✅ Security headers implemented
- ✅ Attack protection active
- ✅ Session security enabled
- ✅ File upload protection
- ✅ API security configured
- ✅ Monitoring system active
- ✅ Management commands available

Your Laravel application is now protected with enterprise-level security measures.
