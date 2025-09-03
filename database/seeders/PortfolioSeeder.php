<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Portfolio;
use App\Models\PortfolioCategory;
use Carbon\Carbon;

class PortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Очищаем таблицу перед заполнением
        Portfolio::truncate();

        // Получаем все категории
        $categories = PortfolioCategory::all()->keyBy('slug');

        $portfolios = [
            // Web Development
            [
                'title' => 'Modern E-commerce Platform',
                'slug' => 'modern-ecommerce-platform',
                'short_description' => 'Comprehensive online store with advanced features and responsive design.',
                'description' => 'A fully-featured e-commerce platform built with modern technologies. Features include user authentication, product catalog with advanced filtering, shopping cart functionality, secure payment processing, order management system, and comprehensive admin dashboard. The platform supports multiple payment methods, real-time inventory tracking, and provides detailed analytics for business insights.',
                'category_slug' => 'web-development',
                'client' => 'RetailTech Solutions',
                'image_url' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1563013544-824ae1b704d3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1556742111-a301076d9d18?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'technologies' => ['Vue.js', 'Laravel', 'MySQL', 'Tailwind CSS', 'Stripe API', 'Redis'],
                'completed_at' => Carbon::create(2025, 8, 15),
                'is_featured' => true,
                'is_published' => true
            ],

            // Mobile Applications
            [
                'title' => 'HealthTracker Mobile App',
                'slug' => 'healthtracker-mobile-app',
                'short_description' => 'Comprehensive health and fitness tracking application.',
                'description' => 'A comprehensive mobile application for health and fitness tracking. Features include workout planning, nutrition tracking, progress monitoring, goal setting, and social features to connect with other users. The app includes integration with wearable devices, personalized recommendations, and detailed analytics to help users achieve their health goals.',
                'category_slug' => 'mobile-applications',
                'client' => 'FitLife Inc.',
                'image_url' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1434494878577-86c23bcb06b9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'technologies' => ['React Native', 'Firebase', 'Node.js', 'MongoDB', 'Health APIs'],
                'completed_at' => Carbon::create(2025, 8, 22),
                'is_featured' => true,
                'is_published' => true
            ],

            // E-commerce Solutions
            [
                'title' => 'Luxury Fashion Boutique',
                'slug' => 'luxury-fashion-boutique',
                'short_description' => 'Premium fashion e-commerce with advanced styling features.',
                'description' => 'An elegant e-commerce solution for luxury fashion brands. Features include high-quality product galleries, virtual styling consultations, size recommendation engine, wishlist functionality, and premium customer service integration. The platform emphasizes visual storytelling and brand experience while maintaining excellent performance and user experience.',
                'category_slug' => 'ecommerce-solutions',
                'client' => 'Elegance Fashion House',
                'image_url' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'technologies' => ['Shopify Plus', 'JavaScript', 'CSS3', 'Payment APIs', 'CDN'],
                'completed_at' => Carbon::create(2025, 8, 28),
                'is_featured' => false,
                'is_published' => true
            ],

            // Business Applications
            [
                'title' => 'Enterprise CRM System',
                'slug' => 'enterprise-crm-system',
                'short_description' => 'Comprehensive customer relationship management platform.',
                'description' => 'A powerful CRM solution designed for enterprise-level sales teams. Includes lead management, contact organization, deal pipeline tracking, email integration, automated workflows, and comprehensive reporting dashboards. The system provides detailed insights into sales performance, customer interactions, and helps optimize sales processes for maximum efficiency.',
                'category_slug' => 'business-applications',
                'client' => 'SalesForce Pro',
                'image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1504868584819-f8e8b4b6d7e3?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'technologies' => ['Laravel', 'Vue.js', 'PostgreSQL', 'Chart.js', 'Email APIs'],
                'completed_at' => Carbon::create(2025, 9, 1),
                'is_featured' => true,
                'is_published' => true
            ],

            // Landing Pages
            [
                'title' => 'SaaS Product Launch Page',
                'slug' => 'saas-product-launch-page',
                'short_description' => 'High-converting landing page with modern design.',
                'description' => 'A conversion-optimized landing page designed for a SaaS product launch. Features compelling copywriting, interactive product demos, social proof elements, email capture forms, and detailed analytics tracking. The page is optimized for maximum conversion rates with A/B testing capabilities and mobile-first responsive design.',
                'category_slug' => 'landing-pages',
                'client' => 'CloudTech Startup',
                'image_url' => 'https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'technologies' => ['HTML5', 'CSS3', 'JavaScript', 'Google Analytics', 'Optimization Tools'],
                'completed_at' => Carbon::create(2025, 8, 12),
                'is_featured' => false,
                'is_published' => true
            ],

            // Portfolio Websites
            [
                'title' => 'Creative Artist Portfolio',
                'slug' => 'creative-artist-portfolio',
                'short_description' => 'Stunning portfolio website showcasing artistic works.',
                'description' => 'A visually striking portfolio website for a creative artist featuring an interactive gallery, project showcases, artist biography, exhibition history, and contact functionality. The design emphasizes visual impact and storytelling while maintaining excellent user experience across all devices. Features include image optimization and smooth animations.',
                'category_slug' => 'portfolio-websites',
                'client' => 'Marina Art Studio',
                'image_url' => 'https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1551739440-5dd934d3a94a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                ],
                'technologies' => ['Next.js', 'React', 'CSS3', 'Image Optimization', 'Animation Libraries'],
                'completed_at' => Carbon::create(2025, 8, 20),
                'is_featured' => true,
                'is_published' => true
            ],

            // API Development
            [
                'title' => 'RESTful API Service',
                'slug' => 'restful-api-service',
                'short_description' => 'Scalable API with comprehensive documentation.',
                'description' => 'A robust and scalable RESTful API service designed for high performance and reliability. Features include JWT authentication, rate limiting, comprehensive API documentation, versioning support, error handling, and monitoring capabilities. The API supports multiple data formats and includes automated testing suite for reliability.',
                'category_slug' => 'api-development',
                'client' => 'DataFlow Technologies',
                'image_url' => 'https://images.unsplash.com/photo-1518432031352-d6fc5c10da5a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                'technologies' => ['Node.js', 'Express', 'MongoDB', 'JWT', 'Swagger', 'Docker'],
                'completed_at' => Carbon::create(2025, 8, 25),
                'is_featured' => false,
                'is_published' => true
            ]
        ];

        foreach ($portfolios as $portfolioData) {
            $categorySlug = $portfolioData['category_slug'];
            unset($portfolioData['category_slug']);

            if (isset($categories[$categorySlug])) {
                $portfolioData['portfolio_category_id'] = $categories[$categorySlug]->id;
                $portfolioData['category'] = $categories[$categorySlug]->name;

                Portfolio::create($portfolioData);
                echo "Created portfolio: {$portfolioData['title']}\n";
            }
        }

        echo "Portfolio seeding completed successfully!\n";
    }
}
