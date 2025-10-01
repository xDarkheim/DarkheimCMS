<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyInfo;

class CompanyInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyInfos = [
            // Contact Information
            [
                'key' => 'company_email',
                'label' => 'Company Email',
                'value' => 'darkheim.studio@gmail.com',
                'type' => 'email',
                'icon' => 'fas fa-envelope',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'company_phone',
                'label' => 'Company Phone',
                'value' => '+1 (555) 123-4567',
                'type' => 'phone',
                'icon' => 'fas fa-phone',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'company_address',
                'label' => 'Company Address',
                'value' => 'San Francisco, CA',
                'type' => 'address',
                'icon' => 'fas fa-map-marker-alt',
                'is_active' => true,
                'sort_order' => 3,
            ],

            // Social Media Links
            [
                'key' => 'social_github',
                'label' => 'GitHub',
                'value' => 'https://github.com/darkheim-studio',
                'type' => 'social',
                'icon' => 'fab fa-github',
                'is_active' => true,
                'sort_order' => 4,
                'metadata' => [
                    'platform' => 'github',
                    'color' => '#333333'
                ]
            ],
            [
                'key' => 'social_linkedin',
                'label' => 'LinkedIn',
                'value' => 'https://linkedin.com/company/darkheim-studio',
                'type' => 'social',
                'icon' => 'fab fa-linkedin',
                'is_active' => true,
                'sort_order' => 5,
                'metadata' => [
                    'platform' => 'linkedin',
                    'color' => '#0077b5'
                ]
            ],
            [
                'key' => 'social_twitter',
                'label' => 'Twitter',
                'value' => 'https://twitter.com/darkheim_studio',
                'type' => 'social',
                'icon' => 'fab fa-twitter',
                'is_active' => true,
                'sort_order' => 6,
                'metadata' => [
                    'platform' => 'twitter',
                    'color' => '#1da1f2'
                ]
            ],
            [
                'key' => 'social_instagram',
                'label' => 'Instagram',
                'value' => 'https://instagram.com/darkheim.studio',
                'type' => 'social',
                'icon' => 'fab fa-instagram',
                'is_active' => true,
                'sort_order' => 7,
                'metadata' => [
                    'platform' => 'instagram',
                    'color' => '#e4405f'
                ]
            ],
            [
                'key' => 'social_discord',
                'label' => 'Discord',
                'value' => 'https://discord.gg/darkheim',
                'type' => 'social',
                'icon' => 'fab fa-discord',
                'is_active' => true,
                'sort_order' => 8,
                'metadata' => [
                    'platform' => 'discord',
                    'color' => '#5865f2'
                ]
            ],
            [
                'key' => 'social_youtube',
                'label' => 'YouTube',
                'value' => 'https://youtube.com/@darkheim-studio',
                'type' => 'social',
                'icon' => 'fab fa-youtube',
                'is_active' => true,
                'sort_order' => 9,
                'metadata' => [
                    'platform' => 'youtube',
                    'color' => '#ff0000'
                ]
            ],

            // Additional Company Info
            [
                'key' => 'company_description',
                'label' => 'Company Description',
                'value' => 'We create exceptional digital experiences that drive business growth and user engagement. Our team combines creativity with technical expertise to deliver outstanding results.',
                'type' => 'textarea',
                'icon' => 'fas fa-info-circle',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'key' => 'response_time',
                'label' => 'Response Time',
                'value' => 'We typically respond within 24 hours during business days. For urgent matters, please call us directly.',
                'type' => 'text',
                'icon' => 'fas fa-clock',
                'is_active' => true,
                'sort_order' => 11,
            ],
        ];

        foreach ($companyInfos as $info) {
            CompanyInfo::updateOrCreate(
                ['key' => $info['key']],
                $info
            );
        }
    }
}
