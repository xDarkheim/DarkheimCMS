<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use App\Models\PortfolioCategory;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing categories
        $categories = PortfolioCategory::all();

        if ($categories->isEmpty()) {
            $this->command->error('No portfolio categories found. Please seed portfolio categories first.');
            return;
        }

        $portfolios = [
            [
                'title' => 'E-commerce Website',
                'slug' => 'e-commerce-website',
                'description' => 'A modern e-commerce platform built with Laravel and Vue.js. Features include product catalog, shopping cart, payment integration, order management, and admin dashboard.',
                'short_description' => 'Modern e-commerce platform with full shopping functionality',
                'image_url' => 'https://via.placeholder.com/800x600/667eea/ffffff?text=E-commerce+Website',
                'project_url' => 'https://example-ecommerce.com',
                'github_url' => 'https://github.com/example/ecommerce',
                'technologies' => ['Laravel', 'Vue.js', 'MySQL', 'Stripe API', 'Tailwind CSS'],
                'client' => 'Tech Startup Inc.',
                'completed_at' => now()->subMonths(2),
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 1,
                'portfolio_category_id' => $categories->random()->id,
            ],
            [
                'title' => 'Mobile App Dashboard',
                'slug' => 'mobile-app-dashboard',
                'description' => 'React Native mobile application with comprehensive dashboard for business analytics. Includes real-time data visualization, user management, and push notifications.',
                'short_description' => 'Business analytics mobile app with real-time dashboard',
                'image_url' => 'https://via.placeholder.com/800x600/4facfe/ffffff?text=Mobile+Dashboard',
                'project_url' => 'https://example-mobile.com',
                'github_url' => 'https://github.com/example/mobile-dashboard',
                'technologies' => ['React Native', 'Node.js', 'MongoDB', 'Socket.io', 'Chart.js'],
                'client' => 'Business Solutions LLC',
                'completed_at' => now()->subMonths(1),
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 2,
                'portfolio_category_id' => $categories->random()->id,
            ],
            [
                'title' => 'Corporate Website Redesign',
                'slug' => 'corporate-website-redesign',
                'description' => 'Complete redesign and development of corporate website with modern design principles. Features responsive design, CMS integration, and SEO optimization.',
                'short_description' => 'Modern corporate website with responsive design',
                'image_url' => 'https://via.placeholder.com/800x600/43e97b/ffffff?text=Corporate+Website',
                'project_url' => 'https://example-corporate.com',
                'github_url' => null,
                'technologies' => ['HTML5', 'CSS3', 'JavaScript', 'PHP', 'WordPress'],
                'client' => 'Corporate Enterprises',
                'completed_at' => now()->subWeeks(3),
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 3,
                'portfolio_category_id' => $categories->random()->id,
            ],
            [
                'title' => 'API Development Project',
                'slug' => 'api-development-project',
                'description' => 'RESTful API development for third-party integrations. Includes authentication, rate limiting, documentation, and comprehensive testing suite.',
                'short_description' => 'RESTful API with comprehensive documentation',
                'image_url' => 'https://via.placeholder.com/800x600/fa709a/ffffff?text=API+Development',
                'project_url' => null,
                'github_url' => 'https://github.com/example/api-project',
                'technologies' => ['Laravel', 'MySQL', 'Redis', 'Docker', 'Postman'],
                'client' => null,
                'completed_at' => now()->subWeeks(2),
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 4,
                'portfolio_category_id' => $categories->random()->id,
            ],
            [
                'title' => 'SaaS Platform MVP',
                'slug' => 'saas-platform-mvp',
                'description' => 'Minimum Viable Product for a SaaS platform including user authentication, subscription management, team collaboration features, and admin panel.',
                'short_description' => 'SaaS platform MVP with subscription management',
                'image_url' => 'https://via.placeholder.com/800x600/fee140/333333?text=SaaS+Platform',
                'project_url' => 'https://example-saas.com',
                'github_url' => null,
                'technologies' => ['Next.js', 'TypeScript', 'PostgreSQL', 'Stripe', 'Tailwind CSS'],
                'client' => 'Startup Ventures',
                'completed_at' => now()->subDays(10),
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 5,
                'portfolio_category_id' => $categories->random()->id,
            ],
        ];

        foreach ($portfolios as $portfolioData) {
            Portfolio::create($portfolioData);
        }

        $this->command->info('Portfolio seeder completed successfully!');
        $this->command->info('Created ' . count($portfolios) . ' portfolio items.');
    }
}
