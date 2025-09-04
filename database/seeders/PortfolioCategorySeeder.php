<?php

namespace Database\Seeders;

use App\Models\PortfolioCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PortfolioCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Custom websites, web applications, and e-commerce solutions',
                'icon' => 'fas fa-globe',
                'color' => '#3b82f6',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Mobile Applications',
                'slug' => 'mobile-applications',
                'description' => 'iOS and Android mobile app development',
                'icon' => 'fas fa-mobile-alt',
                'color' => '#10b981',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'E-commerce Solutions',
                'slug' => 'ecommerce-solutions',
                'description' => 'Online stores and e-commerce platforms',
                'icon' => 'fas fa-shopping-cart',
                'color' => '#f59e0b',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Business Applications',
                'slug' => 'business-applications',
                'description' => 'CRM, ERP, and custom business management systems',
                'icon' => 'fas fa-briefcase',
                'color' => '#8b5cf6',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Landing Pages',
                'slug' => 'landing-pages',
                'description' => 'Marketing and promotional single-page websites',
                'icon' => 'fas fa-rocket',
                'color' => '#ef4444',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Portfolio Websites',
                'slug' => 'portfolio-websites',
                'description' => 'Personal and professional portfolio showcases',
                'icon' => 'fas fa-image',
                'color' => '#06b6d4',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'API Development',
                'slug' => 'api-development',
                'description' => 'REST APIs and backend services',
                'icon' => 'fas fa-code',
                'color' => '#64748b',
                'is_active' => true,
                'sort_order' => 7,
            ]
        ];

        $created = 0;
        $updated = 0;

        foreach ($categories as $categoryData) {
            $category = PortfolioCategory::firstOrCreate(
                ['slug' => $categoryData['slug']], // Поиск по slug
                $categoryData // Данные для создания
            );

            if ($category->wasRecentlyCreated) {
                $created++;
                $this->command->info('Created category: ' . $categoryData['name'] . ' (' . $categoryData['slug'] . ')');
            } else {
                // Обновляем существующую категорию, кроме slug
                $updateData = $categoryData;
                unset($updateData['slug']); // Не обновляем slug
                $category->update($updateData);
                $updated++;
                $this->command->info('Updated category: ' . $categoryData['name'] . ' (' . $categoryData['slug'] . ')');
            }
        }

        $this->command->info('Portfolio categories seeder completed successfully!');
        $this->command->info('Created ' . $created . ' new categories.');
        $this->command->info('Updated ' . $updated . ' existing categories.');
        $this->command->info('All portfolio data remains intact.');
    }
}
