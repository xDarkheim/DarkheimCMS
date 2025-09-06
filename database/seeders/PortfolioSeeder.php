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
        // Clear existing portfolios to prevent duplication issues
        $this->command->info('Clearing existing portfolios...');
        Portfolio::truncate();

        // Get existing categories
        $categories = PortfolioCategory::where('is_active', true)->get();

        if ($categories->isEmpty()) {
            $this->command->error('No portfolio categories found. Please seed portfolio categories first.');
            return;
        }

        // Create a mapping of category slugs for easier access
        $categoryMap = $categories->keyBy('slug');

        $portfolios = [
            // Featured Projects (sort_order 1-5)
            [
                'title' => 'TechCorp E-commerce Platform',
                'slug' => 'techcorp-ecommerce-platform',
                'description' => 'A comprehensive e-commerce platform built with Laravel and Vue.js. Features include advanced product catalog with filtering and search, multi-vendor support, real-time inventory management, integrated payment processing with Stripe and PayPal, order tracking system, customer reviews and ratings, wishlist functionality, and a powerful admin dashboard with analytics. The platform also includes automated email notifications, coupon system, and mobile-responsive design.',
                'short_description' => 'Multi-vendor e-commerce platform with advanced features and analytics',
                'image_url' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop&crop=entropy',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://demo-ecommerce.techcorp.com',
                'github_url' => 'https://github.com/techcorp/ecommerce-platform',
                'technologies' => ['Laravel', 'Vue.js', 'MySQL', 'Redis', 'Stripe API', 'PayPal API', 'Tailwind CSS', 'Docker'],
                'category' => 'web-development',
                'client' => 'TechCorp Solutions',
                'completed_at' => now()->subMonths(2),
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 1, // Featured проекты идут первыми
                'portfolio_category_id' => $categoryMap->get('web-development')?->id,
            ],
            [
                'title' => 'FinanceTracker Mobile App',
                'slug' => 'financetracker-mobile-app',
                'description' => 'A comprehensive personal finance management mobile application built with React Native. Features include expense tracking with category-based organization, budget planning and monitoring, bill reminders and notifications, financial goal setting and tracking, bank account integration via secure APIs, detailed spending analytics with charts and graphs, receipt scanning with OCR technology, and multi-currency support. The app also includes biometric authentication and offline functionality.',
                'short_description' => 'Personal finance management app with bank integration and analytics',
                'image_url' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&h=600&fit=crop',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://apps.apple.com/app/financetracker',
                'github_url' => null,
                'technologies' => ['React Native', 'TypeScript', 'Node.js', 'MongoDB', 'Express.js', 'Socket.io', 'Plaid API'],
                'category' => 'mobile-applications',
                'client' => 'FinTech Startup',
                'completed_at' => now()->subMonths(1),
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 2, // Featured
                'portfolio_category_id' => $categoryMap->get('mobile-applications')?->id,
            ],
            [
                'title' => 'CRM Management System',
                'slug' => 'crm-management-system-2025',
                'description' => 'A powerful Customer Relationship Management system built specifically for mid-size businesses. The application features comprehensive customer data management, sales pipeline tracking with customizable stages, automated email marketing campaigns, detailed reporting and analytics dashboard, task and appointment scheduling, document management with version control, integration with popular email providers and calendar systems, and role-based access control. The system also includes API integrations with accounting software and third-party tools.',
                'short_description' => 'Enterprise CRM system with sales pipeline and marketing automation',
                'image_url' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&h=600&fit=crop',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://demo-crm.businesssolutions.com',
                'github_url' => null,
                'technologies' => ['Laravel', 'Vue.js', 'PostgreSQL', 'Redis', 'Elasticsearch', 'Chart.js', 'WebSockets'],
                'category' => 'business-applications',
                'client' => 'Business Solutions LLC',
                'completed_at' => now()->subMonths(3),
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 3, // Featured
                'portfolio_category_id' => $categoryMap->get('business-applications')?->id,
            ],
            [
                'title' => 'Fashion E-commerce Store',
                'slug' => 'fashion-ecommerce-store-2025',
                'description' => 'A sophisticated e-commerce platform specialized for fashion retail. Features include advanced product visualization with 360-degree views, size guide integration, virtual try-on capabilities, personalized recommendations based on browsing history, wishlist and favorites functionality, social sharing integration, inventory management across multiple warehouses, and comprehensive analytics dashboard. The platform also includes subscription box functionality and loyalty program management.',
                'short_description' => 'Fashion-focused e-commerce with virtual try-on and personalization',
                'image_url' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=600&fit=crop',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1445205170230-053b83016050?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://fashionstore.boutique.com',
                'github_url' => null,
                'technologies' => ['Shopify Plus', 'React', 'GraphQL', 'Node.js', 'PostgreSQL', 'Stripe', 'AWS S3'],
                'category' => 'ecommerce-solutions',
                'client' => 'Fashion Boutique Chain',
                'completed_at' => now()->subDays(5),
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 4, // Featured
                'portfolio_category_id' => $categoryMap->get('ecommerce-solutions')?->id,
            ],

            // Regular Projects (sort_order 5+)
            [
                'title' => 'Modern Corporate Website',
                'slug' => 'modern-corporate-website',
                'description' => 'Complete redesign and development of a corporate website for a Fortune 500 company. The project involved creating a modern, responsive design that reflects the company\'s brand identity, implementing a custom CMS for easy content management, optimizing for SEO and performance, integrating with third-party APIs for real-time data, and ensuring accessibility compliance (WCAG 2.1). The site features interactive elements, dynamic content loading, and comprehensive analytics tracking.',
                'short_description' => 'Modern corporate website with custom CMS and SEO optimization',
                'image_url' => 'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?w=800&h=600&fit=crop',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://corporate-enterprise.com',
                'github_url' => null,
                'technologies' => ['Laravel', 'Blade Templates', 'MySQL', 'SCSS', 'JavaScript', 'jQuery', 'Bootstrap'],
                'category' => 'web-development',
                'client' => 'Enterprise Corporation',
                'completed_at' => now()->subMonths(4),
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 5,
                'portfolio_category_id' => $categoryMap->get('web-development')?->id,
            ],
            [
                'title' => 'Fitness Tracking App',
                'slug' => 'fitness-tracking-app-2025',
                'description' => 'A comprehensive fitness and health tracking mobile application developed using Flutter for cross-platform compatibility. The app includes workout planning and tracking, nutrition logging with barcode scanning, progress monitoring with detailed statistics, social features for sharing achievements, integration with wearable devices, GPS tracking for running and cycling, custom workout creation, and AI-powered recommendations based on user behavior and goals.',
                'short_description' => 'Cross-platform fitness app with social features and device integration',
                'image_url' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&h=600&fit=crop',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1594737625785-a6cbdabd333c?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://play.google.com/store/apps/fitnesstracker',
                'github_url' => 'https://github.com/fitness/tracker-app',
                'technologies' => ['Flutter', 'Dart', 'Firebase', 'Google Fit API', 'Apple HealthKit', 'SQLite'],
                'category' => 'mobile-applications',
                'client' => 'Health & Wellness Co.',
                'completed_at' => now()->subWeeks(3),
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 6,
                'portfolio_category_id' => $categoryMap->get('mobile-applications')?->id,
            ],
            [
                'title' => 'Multi-Store Marketplace Platform',
                'slug' => 'multi-store-marketplace-platform',
                'description' => 'A comprehensive marketplace platform that allows multiple vendors to sell their products through a single unified interface. Features include vendor management system, commission tracking, automated payouts, product approval workflows, dispute resolution system, advanced search and filtering, real-time chat between buyers and sellers, integrated shipping solutions, and comprehensive analytics for both administrators and vendors.',
                'short_description' => 'Multi-vendor marketplace with vendor management and analytics',
                'image_url' => 'https://images.unsplash.com/photo-1556742502-ec7c0e9f34b1?w=800&h=600&fit=crop',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1556742111-a301076d9d18?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://marketplace-demo.example.com',
                'github_url' => null,
                'technologies' => ['Laravel', 'Vue.js', 'MySQL', 'Redis', 'Elasticsearch', 'Stripe Connect', 'AWS'],
                'category' => 'ecommerce-solutions',
                'client' => 'Marketplace Ventures',
                'completed_at' => now()->subWeeks(6),
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 7,
                'portfolio_category_id' => $categoryMap->get('ecommerce-solutions')?->id,
            ],
            [
                'title' => 'Project Management Suite',
                'slug' => 'project-management-suite',
                'description' => 'A comprehensive project management application designed for software development teams and agencies. Features include Kanban and Gantt chart views, time tracking with detailed reporting, team collaboration tools, file sharing and version control, client portal for project updates, automated invoicing based on tracked hours, resource allocation and capacity planning, integration with popular development tools like GitHub and Slack, and customizable workflows for different project types.',
                'short_description' => 'Full-featured project management suite with time tracking and invoicing',
                'image_url' => 'https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=800&h=600&fit=crop',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://project-suite-demo.com',
                'github_url' => null,
                'technologies' => ['React', 'Node.js', 'MongoDB', 'Socket.io', 'Chart.js', 'Docker', 'GitHub API'],
                'category' => 'business-applications',
                'client' => 'DevTeam Solutions',
                'completed_at' => now()->subWeeks(8),
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 8,
                'portfolio_category_id' => $categoryMap->get('business-applications')?->id,
            ],
            [
                'title' => 'SaaS Product Landing Page',
                'slug' => 'saas-product-landing-page-2025',
                'description' => 'A high-converting landing page for a SaaS product launch. The page features modern animations and micro-interactions, optimized conversion funnels, A/B testing capabilities, integrated analytics tracking, lead capture forms with CRM integration, customer testimonials and social proof sections, pricing tables with interactive features, and mobile-first responsive design. The landing page achieved a 23% conversion rate improvement over the previous version.',
                'short_description' => 'High-converting SaaS landing page with A/B testing and analytics',
                'image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&h=600&fit=crop',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://saas-launch.example.com',
                'github_url' => null,
                'technologies' => ['HTML5', 'CSS3', 'JavaScript', 'GSAP', 'Google Analytics', 'Mailchimp API'],
                'category' => 'landing-pages',
                'client' => 'StartupVentures',
                'completed_at' => now()->subWeeks(2),
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 9,
                'portfolio_category_id' => $categoryMap->get('landing-pages')?->id,
            ],
            [
                'title' => 'Real Estate Agency Landing Page',
                'slug' => 'real-estate-landing-page',
                'description' => 'A professional landing page for a premium real estate agency specializing in luxury properties. Features include property search functionality, virtual tour integration, agent profiles with contact forms, neighborhood guides, mortgage calculator, property valuation tool, newsletter signup, and SEO optimization for local search. The design emphasizes trust and professionalism with high-quality imagery and smooth user experience.',
                'short_description' => 'Premium real estate landing page with property search and tools',
                'image_url' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&h=600&fit=crop',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1582407947304-fd86f028f716?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://luxury-realestate.example.com',
                'github_url' => null,
                'technologies' => ['Next.js', 'React', 'Tailwind CSS', 'Google Maps API', 'Strapi CMS'],
                'category' => 'landing-pages',
                'client' => 'Luxury Properties Group',
                'completed_at' => now()->subWeeks(4),
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 10,
                'portfolio_category_id' => $categoryMap->get('landing-pages')?->id,
            ],
            [
                'title' => 'Artist Portfolio Website',
                'slug' => 'artist-portfolio-website-2025',
                'description' => 'A stunning portfolio website for a professional photographer and digital artist. Features include a dynamic gallery with lightbox functionality, category-based filtering system, client testimonials section, contact form with inquiry management, blog section for artistic insights, SEO optimization for better visibility, social media integration, and a custom admin panel for easy content management. The design emphasizes visual storytelling with smooth transitions and immersive user experience.',
                'short_description' => 'Visual portfolio website with dynamic gallery and content management',
                'image_url' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800&h=600&fit=crop',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1542744094-24638eff58bb?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://artistname.portfolio.com',
                'github_url' => null,
                'technologies' => ['Next.js', 'React', 'Tailwind CSS', 'Sanity CMS', 'Vercel', 'Framer Motion'],
                'category' => 'portfolio-websites',
                'client' => 'Professional Artist',
                'completed_at' => now()->subDays(15),
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 11,
                'portfolio_category_id' => $categoryMap->get('portfolio-websites')?->id,
            ],
            [
                'title' => 'Architecture Firm Portfolio',
                'slug' => 'architecture-firm-portfolio',
                'description' => 'A sophisticated portfolio website for an award-winning architecture firm. Features include project showcases with 3D visualizations, before/after comparisons, interactive floor plans, team member profiles, awards and recognition section, news and insights blog, project timeline visualization, client testimonials, and a comprehensive contact system. The design reflects modern architectural principles with clean lines and strategic use of whitespace.',
                'short_description' => 'Architectural portfolio with 3D visualizations and project timelines',
                'image_url' => 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?w=800&h=600&fit=crop',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://architecture-portfolio.example.com',
                'github_url' => null,
                'technologies' => ['Gatsby', 'React', 'Three.js', 'Styled Components', 'Contentful', 'Netlify'],
                'category' => 'portfolio-websites',
                'client' => 'Modern Architecture Studio',
                'completed_at' => now()->subDays(20),
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 12,
                'portfolio_category_id' => $categoryMap->get('portfolio-websites')?->id,
            ],
            [
                'title' => 'Multi-Service API Gateway',
                'slug' => 'multi-service-api-gateway-2025',
                'description' => 'A comprehensive API gateway solution designed to handle microservices architecture. Features include request routing and load balancing, authentication and authorization with JWT tokens, rate limiting and throttling, request/response transformation, logging and monitoring with detailed metrics, API versioning support, caching mechanisms, and comprehensive documentation with interactive testing interface. The gateway also includes health checks, circuit breaker patterns, and integration with popular monitoring tools.',
                'short_description' => 'Scalable API gateway for microservices with comprehensive monitoring',
                'image_url' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&h=600&fit=crop',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://api-docs.techservices.com',
                'github_url' => 'https://github.com/techservices/api-gateway',
                'technologies' => ['Node.js', 'Express.js', 'Redis', 'MongoDB', 'Docker', 'Kubernetes', 'Swagger', 'Jest'],
                'category' => 'api-development',
                'client' => 'Tech Services Inc.',
                'completed_at' => now()->subMonths(1),
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 13,
                'portfolio_category_id' => $categoryMap->get('api-development')?->id,
            ],
            [
                'title' => 'Payment Processing API',
                'slug' => 'payment-processing-api',
                'description' => 'A secure and robust payment processing API designed for e-commerce platforms and financial applications. Features include support for multiple payment methods (cards, digital wallets, bank transfers), PCI DSS compliance, fraud detection and prevention, real-time transaction monitoring, webhook notifications, comprehensive transaction reporting, refund and chargeback management, and multi-currency support. The API includes extensive documentation and SDKs for popular programming languages.',
                'short_description' => 'Secure payment API with fraud detection and multi-currency support',
                'image_url' => 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=800&h=600&fit=crop',
                'gallery_images' => [
                    'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=800&h=600&fit=crop'
                ],
                'project_url' => 'https://payments-api-docs.example.com',
                'github_url' => null,
                'technologies' => ['Python', 'FastAPI', 'PostgreSQL', 'Redis', 'Celery', 'Stripe', 'Docker', 'AWS'],
                'category' => 'api-development',
                'client' => 'FinTech Solutions',
                'completed_at' => now()->subDays(10),
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 14,
                'portfolio_category_id' => $categoryMap->get('api-development')?->id,
            ],
        ];

        // Create portfolios
        $created = 0;

        foreach ($portfolios as $portfolioData) {
            // Skip if category doesn't exist
            if (!$portfolioData['portfolio_category_id']) {
                $this->command->warn('Skipping portfolio: ' . $portfolioData['title'] . ' (category not found)');
                continue;
            }

            Portfolio::create($portfolioData);
            $created++;
            $this->command->info('Created: ' . $portfolioData['title']);
        }

        $this->command->info('Portfolio seeder completed successfully!');
        $this->command->info('Created ' . $created . ' portfolio items with balanced distribution:');
        $this->command->info('- Web Development: 2 projects');
        $this->command->info('- Mobile Applications: 2 projects');
        $this->command->info('- E-commerce Solutions: 2 projects');
        $this->command->info('- Business Applications: 2 projects');
        $this->command->info('- Landing Pages: 2 projects');
        $this->command->info('- Portfolio Websites: 2 projects');
        $this->command->info('- API Development: 2 projects');
    }
}
