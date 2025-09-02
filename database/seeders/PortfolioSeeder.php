<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $portfolios = [
            [
                'title' => 'Local Bakery Website',
                'slug' => 'local-bakery-website',
                'short_description' => 'Modern responsive website for a local bakery with online ordering capabilities.',
                'description' => 'A clean and modern website for Sunrise Bakery featuring their product catalog, online ordering system, and store information. Built with Laravel backend and Vue.js frontend, styled with custom Sass. The site includes a simple CMS for easy content updates and basic e-commerce functionality.',
                'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'project_url' => 'https://sunrisebakery-demo.darkheim.dev',
                'github_url' => 'https://github.com/darkheim-studio/bakery-website',
                'technologies' => ['PHP', 'Laravel', 'Vue.js', 'MySQL', 'HTML5', 'CSS', 'Sass', 'Git', 'Linux'],
                'category' => 'web',
                'client' => 'Sunrise Bakery',
                'completed_at' => '2024-12-10',
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Portfolio Website for Photographer',
                'slug' => 'photographer-portfolio',
                'short_description' => 'Elegant portfolio website showcasing photography work with gallery features.',
                'description' => 'A minimalist and elegant portfolio website for professional photographer Maria Santos. Features include image galleries, client testimonials, contact forms, and booking system. Optimized for image loading and mobile viewing. Deployed using Docker containers on Linux server.',
                'image_url' => 'https://images.unsplash.com/photo-1452587925148-ce544e77e70d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1452587925148-ce544e77e70d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'project_url' => 'https://mariasantos-photo.darkheim.dev',
                'github_url' => 'https://github.com/darkheim-studio/photographer-portfolio',
                'technologies' => ['PHP', 'Laravel', 'JavaScript', 'Vue.js', 'MariaDB', 'HTML5', 'Sass', 'Docker', 'Git', 'Linux'],
                'category' => 'web',
                'client' => 'Maria Santos Photography',
                'completed_at' => '2024-11-25',
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Small Business Management System',
                'slug' => 'business-management-system',
                'short_description' => 'Simple CRM and inventory management system for small businesses.',
                'description' => 'A lightweight business management system built for a local auto repair shop. Includes customer management, service tracking, inventory control, and basic reporting. The system runs on Windows Server environment and uses Git for version control throughout development.',
                'image_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'project_url' => null,
                'github_url' => 'https://github.com/darkheim-studio/business-crm',
                'technologies' => ['PHP', 'Laravel', 'Vue.js', 'MySQL', 'HTML5', 'CSS', 'JavaScript', 'Git', 'Windows Server'],
                'category' => 'web',
                'client' => 'AutoFix Garage',
                'completed_at' => '2024-10-08',
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Company Landing Page',
                'slug' => 'company-landing-page',
                'short_description' => 'Modern landing page for a consulting company with contact forms.',
                'description' => 'A professional landing page for TechConsult Pro featuring company services, team information, and lead generation forms. Built with focus on performance and SEO optimization. Styled with custom Sass and deployed using Docker containers.',
                'image_url' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'project_url' => 'https://techconsultpro.darkheim.dev',
                'github_url' => null,
                'technologies' => ['PHP', 'Laravel', 'HTML5', 'CSS', 'Sass', 'JavaScript', 'MySQL', 'Docker', 'Git', 'Linux'],
                'category' => 'web',
                'client' => 'TechConsult Pro',
                'completed_at' => '2024-09-15',
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Restaurant Menu Website',
                'slug' => 'restaurant-menu-website',
                'short_description' => 'Digital menu website with QR code integration for contactless dining.',
                'description' => 'A digital menu website created during the pandemic for contactless dining experience. Features QR code integration, mobile-optimized design, and easy menu updates. Simple but effective solution for small restaurant business.',
                'image_url' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'project_url' => 'https://pizza-corner-menu.darkheim.dev',
                'github_url' => 'https://github.com/darkheim-studio/restaurant-menu',
                'technologies' => ['PHP', 'Laravel', 'Vue.js', 'MariaDB', 'HTML5', 'Sass', 'Git', 'Linux'],
                'category' => 'web',
                'client' => 'Pizza Corner',
                'completed_at' => '2024-08-20',
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 5,
            ]
        ];

        foreach ($portfolios as $portfolio) {
            Portfolio::create($portfolio);
        }
    }
}
