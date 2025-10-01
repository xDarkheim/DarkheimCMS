<?php

/**
 * Initialize default application settings
 * @author Dmytro Hovenko
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use Symfony\Component\Console\Command\Command as CommandAlias;

class InitializeSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:init {--force : Force reinitialize existing settings}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize default application settings';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Initializing default settings...');

        // Define default settings
        $defaults = [
            // General settings
            'site_name' => ['value' => 'Darkheim Development Studio', 'group' => 'general', 'type' => 'string', 'is_public' => true, 'description' => 'Site name displayed in header'],
            'site_description' => ['value' => 'Professional web development and design services', 'group' => 'general', 'type' => 'string', 'is_public' => true, 'description' => 'Site description for SEO'],
            'admin_email' => ['value' => 'darkheim.studio@gmail.com', 'group' => 'general', 'type' => 'string', 'is_public' => false, 'description' => 'Administrator email address'],
            'items_per_page' => ['value' => 15, 'group' => 'general', 'type' => 'integer', 'is_public' => false, 'description' => 'Number of items per page in admin'],

            // Security settings
            'session_timeout' => ['value' => 60, 'group' => 'security', 'type' => 'integer', 'is_public' => false, 'description' => 'Session timeout in minutes'],
            'max_login_attempts' => ['value' => 5, 'group' => 'security', 'type' => 'integer', 'is_public' => false, 'description' => 'Maximum login attempts before lockout'],
            'require_email_verification' => ['value' => true, 'group' => 'security', 'type' => 'boolean', 'is_public' => false, 'description' => 'Require email verification for new users'],
            'enable_2fa' => ['value' => false, 'group' => 'security', 'type' => 'boolean', 'is_public' => false, 'description' => 'Enable two-factor authentication'],

            // Email settings
            'contact_form_emails' => ['value' => ['darkheim.studio@gmail.com'], 'group' => 'email', 'type' => 'array', 'is_public' => false, 'description' => 'Email addresses to receive contact form submissions'],
            'smtp_enabled' => ['value' => false, 'group' => 'email', 'type' => 'boolean', 'is_public' => false, 'description' => 'Enable SMTP email sending'],
            'smtp_host' => ['value' => '', 'group' => 'email', 'type' => 'string', 'is_public' => false, 'description' => 'SMTP server host'],
            'smtp_port' => ['value' => 587, 'group' => 'email', 'type' => 'integer', 'is_public' => false, 'description' => 'SMTP server port'],

            // API settings
            'api_rate_limit' => ['value' => 100, 'group' => 'api', 'type' => 'integer', 'is_public' => false, 'description' => 'API requests per minute limit'],
            'api_cache_ttl' => ['value' => 3600, 'group' => 'api', 'type' => 'integer', 'is_public' => false, 'description' => 'API cache time to live in seconds'],
        ];

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($defaults as $key => $config) {
            $existing = Setting::where('key', $key)->first();

            if ($existing && !$this->option('force')) {
                $skipped++;
                continue;
            }

            if ($existing) {
                $existing->update($config);
                $updated++;
                $this->line("Updated: $key");
            } else {
                Setting::create(array_merge(['key' => $key], $config));
                $created++;
                $this->line("Created: $key");
            }
        }

        Setting::clearCache();

        $this->info("Settings initialization completed!");
        $this->table(['Action', 'Count'], [
            ['Created', $created],
            ['Updated', $updated],
            ['Skipped', $skipped],
        ]);

        return CommandAlias::SUCCESS;
    }
}
