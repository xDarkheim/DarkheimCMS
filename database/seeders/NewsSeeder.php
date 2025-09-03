<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    public function run()
    {
        // Очищаем существующие новости
        News::truncate();

        $news = [
            [
                'title' => 'Darkheim Development Studio v2.0 - Major Platform Update Released',
                'slug' => 'darkheim-development-studio-v2-major-platform-update-released',
                'content' => 'We\'re excited to announce the release of our completely redesigned development platform and website. This major update brings improved performance, modern design, and enhanced user experience.

Key improvements in v2.0:
- Complete redesign with modern Vue.js components
- Enhanced admin panel with real-time analytics
- Improved project management system
- Better mobile responsiveness
- Advanced portfolio showcase
- Integrated news and blog system
- SEO optimizations
- Performance improvements (50% faster loading times)

The new platform showcases our latest work and provides better insights into our development process. We\'ve implemented cutting-edge technologies to ensure the best possible experience for our visitors and clients.',
                'excerpt' => 'We\'re excited to announce the release of our completely redesigned development platform and website with improved performance and modern design.',
                'image_url' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Darkheim Team',
                'category' => 'releases',
                'is_published' => true,
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(1),
                'views' => 342
            ],
            [
                'title' => 'Completed Advanced E-Commerce Platform with Vue.js Integration',
                'slug' => 'completed-advanced-ecommerce-platform-vuejs-integration',
                'content' => 'We successfully delivered a complex e-commerce solution for a local retail business, featuring real-time inventory management, payment processing, and a modern Vue.js frontend. The project showcases our growing expertise in full-stack development.

The platform includes:
- Real-time inventory tracking with WebSocket integration
- Secure payment gateway integration (Stripe & PayPal)
- Responsive Vue.js frontend with Vuex state management
- Laravel backend with comprehensive REST API
- Advanced admin panel for order and inventory management
- Customer authentication with social login options
- Product catalog with advanced search and filtering
- Shopping cart with persistent sessions
- Order tracking system with email notifications
- Multi-language support

This project was particularly challenging due to the complex business requirements and the need for real-time updates across multiple user interfaces. We used Laravel Broadcast and WebSockets to achieve seamless real-time communication.',
                'excerpt' => 'We successfully delivered a complex e-commerce solution featuring real-time inventory management, payment processing, and a modern Vue.js frontend.',
                'image_url' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Darkheim Team',
                'category' => 'development',
                'is_published' => true,
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(3),
                'views' => 278
            ],
            [
                'title' => 'Adopting Docker and Modern DevOps Practices',
                'slug' => 'adopting-docker-modern-devops-practices',
                'content' => 'Starting this month, we\'re implementing Docker containerization and modern DevOps practices for all new development projects to ensure consistent environments and streamlined deployment processes.

Our new DevOps stack includes:
- Docker containers for development and production
- CI/CD pipelines with GitHub Actions
- Automated testing and deployment
- Infrastructure as Code with Docker Compose
- Container orchestration for scalability
- Monitoring and logging solutions
- Security scanning and vulnerability assessment

Benefits we\'ve observed:
- 80% reduction in environment-related issues
- Faster deployment times (from hours to minutes)
- Improved collaboration between team members
- Better scalability and resource management
- Enhanced security through containerization
- Simplified onboarding for new developers

This transition represents our commitment to using industry best practices and delivering reliable, scalable solutions to our clients.',
                'excerpt' => 'We\'re implementing Docker containerization and modern DevOps practices to ensure consistent environments and streamlined deployment processes.',
                'image_url' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Darkheim Team',
                'category' => 'updates',
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(5),
                'views' => 198
            ],
            [
                'title' => '10 Months of Innovation: Our Development Journey',
                'slug' => '10-months-innovation-development-journey',
                'content' => 'Reflecting on our first 10 months in business - from our humble beginnings to building a reputation for quality web development and innovative solutions in the local technology community.

Our journey highlights:
- Started with a simple WordPress customization project
- Evolved to complex Laravel applications and APIs
- Successfully delivered 12 projects across various industries
- Achieved 100% client satisfaction and retention rate
- Expanded our technology stack and expertise
- Built strong partnerships with local businesses
- Contributed to the local developer community

Technology evolution:
- Month 1-2: WordPress and PHP basics
- Month 3-4: Laravel framework adoption
- Month 5-6: Vue.js frontend development
- Month 7-8: API development and integrations
- Month 9-10: Advanced DevOps and deployment strategies

Client success stories:
- E-commerce platform increasing sales by 150%
- Business management system improving efficiency by 60%
- Portfolio websites generating 200% more leads

Looking ahead, we\'re excited to take on more challenging projects, expand our team, and continue pushing the boundaries of what\'s possible in web development.',
                'excerpt' => 'Reflecting on our first 10 months in business - from humble beginnings to building a reputation for quality web development and innovative solutions.',
                'image_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Darkheim Team',
                'category' => 'behind-scenes',
                'is_published' => true,
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(8),
                'views' => 412
            ],
            [
                'title' => 'Client Success Story: Sunrise Bakery\'s Digital Transformation',
                'slug' => 'client-success-story-sunrise-bakery-digital-transformation',
                'content' => 'Discover how we helped Sunrise Bakery transform their business with a custom online ordering system that increased their sales by 200% during challenging times.

The Challenge:
Sunrise Bakery, a beloved local business with 15 years of history, was struggling to adapt to changing customer preferences and the need for contactless ordering during the pandemic. They needed a solution that would preserve their personal touch while embracing modern technology.

Our Solution:
- Custom online ordering system with real-time inventory
- Mobile-first design for easy smartphone ordering
- Integration with existing POS system
- Customer loyalty program with points and rewards
- Automated email notifications for order updates
- Social media integration for marketing
- Admin dashboard for order and inventory management

Implementation Process:
1. Discovery phase: Understanding business needs and customer behavior
2. Design phase: Creating user-friendly interfaces that reflect the bakery\'s brand
3. Development phase: Building robust backend systems and responsive frontend
4. Testing phase: Ensuring reliability under high load conditions
5. Launch phase: Smooth transition with staff training and customer education

Results after 6 months:
- 200% increase in online orders
- 150% growth in overall sales
- 300% increase in customer base
- 95% customer satisfaction rating
- 40% increase in average order value

"Darkheim didn\'t just build us a website - they gave us a complete digital transformation that saved our business." - Maria Rodriguez, Owner of Sunrise Bakery',
                'excerpt' => 'Discover how we helped Sunrise Bakery transform their business with a custom online ordering system that increased their sales by 200%.',
                'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Darkheim Team',
                'category' => 'community',
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(10),
                'views' => 365
            ],
            [
                'title' => 'Exciting Partnership with Local Tech Hub',
                'slug' => 'exciting-partnership-local-tech-hub',
                'content' => 'We\'re thrilled to announce our new partnership with TechSpace, the premier technology incubator in our city, to support emerging startups with their web development needs.

About the Partnership:
This collaboration will provide startups in the TechSpace incubator program with access to our web development expertise at special rates. We\'ll be offering mentorship, technical consultation, and development services to help these innovative companies bring their ideas to life.

Services we\'ll provide:
- MVP development for early-stage startups
- Technical consultation and architecture planning
- Mentorship on web development best practices
- Workshops on modern development technologies
- Code reviews and optimization services
- Scaling strategies for growing applications

Why this matters:
- Supporting local innovation and entrepreneurship
- Contributing to the growth of our tech community
- Learning from diverse projects and challenges
- Building relationships with future industry leaders
- Giving back to the ecosystem that supports us

We believe in the power of collaboration and community support. This partnership represents our commitment to fostering innovation and helping the next generation of tech companies succeed.

If you\'re a startup looking for reliable web development partners, we\'d love to discuss how we can help bring your vision to life.',
                'excerpt' => 'We\'re thrilled to announce our new partnership with TechSpace to support emerging startups with their web development needs.',
                'image_url' => 'https://images.unsplash.com/photo-1521737711867-e3b97375f902?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Darkheim Team',
                'category' => 'partnerships',
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(12),
                'views' => 187
            ],
            [
                'title' => 'Join Us at the Local Developer Meetup: Building Scalable Web Applications',
                'slug' => 'join-us-local-developer-meetup-building-scalable-web-applications',
                'content' => 'We\'re excited to be presenting at next month\'s Local Developer Meetup! Join us for an in-depth discussion on building scalable web applications with Laravel and Vue.js.

Event Details:
- Date: October 15th, 2025
- Time: 7:00 PM - 9:00 PM
- Location: TechSpace Conference Room
- Topic: "From Idea to Scale: Building Robust Web Applications"

What we\'ll cover:
- Architecture patterns for scalable applications
- Database optimization strategies
- Caching mechanisms and performance tuning
- API design best practices
- Frontend optimization with Vue.js
- Deployment and DevOps considerations
- Real-world case studies from our projects

This presentation will include:
- Live coding demonstrations
- Interactive Q&A session
- Networking opportunities with local developers
- Free pizza and drinks for all attendees
- Resource sharing and community collaboration

Who should attend:
- Web developers of all experience levels
- Students interested in modern web development
- Business owners considering web application development
- Anyone curious about current technology trends

We\'ll be sharing insights from our recent projects, including the challenges we\'ve faced and the solutions we\'ve implemented. This is a great opportunity to connect with the local developer community and learn about the latest trends in web development.

Registration is free but space is limited. Reserve your spot at techspace.com/events',
                'excerpt' => 'Join us at next month\'s Local Developer Meetup for an in-depth discussion on building scalable web applications with Laravel and Vue.js.',
                'image_url' => 'https://images.unsplash.com/photo-1515169067868-5387ec128ce1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Darkheim Team',
                'category' => 'events',
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(15),
                'views' => 156
            ],
            [
                'title' => 'Laravel Best Practices: A Comprehensive Guide for 2025',
                'slug' => 'laravel-best-practices-comprehensive-guide-2025',
                'content' => 'As Laravel continues to evolve, we\'ve compiled our top recommendations for writing clean, maintainable, and efficient Laravel applications in 2025.

1. Project Structure and Organization:
- Follow PSR standards for code formatting
- Use meaningful directory structures
- Implement proper namespace organization
- Separate business logic from controllers
- Use service classes for complex operations

2. Database Best Practices:
- Always use migrations for database changes
- Index frequently queried columns
- Use Eloquent relationships effectively
- Implement database seeding for development
- Consider using database transactions for complex operations

3. Security Considerations:
- Always validate and sanitize user input
- Use Laravel\'s built-in authentication features
- Implement proper authorization with policies
- Protect against common vulnerabilities (SQL injection, XSS, CSRF)
- Keep dependencies updated

4. Performance Optimization:
- Use eager loading to prevent N+1 queries
- Implement caching strategies
- Optimize database queries
- Use Laravel\'s built-in optimization features
- Monitor application performance

5. Testing Strategies:
- Write unit tests for business logic
- Implement feature tests for user workflows
- Use factories for test data generation
- Mock external services in tests
- Maintain good test coverage

This guide represents years of experience working with Laravel projects of all sizes. Following these practices will help you build more reliable, maintainable, and scalable applications.',
                'excerpt' => 'Our comprehensive guide to Laravel best practices for 2025, covering project structure, security, performance, and testing strategies.',
                'image_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Darkheim Team',
                'category' => 'tutorials',
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(18),
                'views' => 445
            ],
            [
                'title' => 'Darkheim Studio Expansion: New Services and Team Growth',
                'slug' => 'darkheim-studio-expansion-new-services-team-growth',
                'content' => 'We\'re excited to announce the expansion of Darkheim Development Studio with new services, team members, and an enhanced focus on delivering cutting-edge web solutions.

New Services:
- Mobile App Development (React Native)
- E-commerce Solutions (Shopify & Custom)
- API Development and Integration
- DevOps and Cloud Infrastructure
- Technical Consulting and Code Audits
- Maintenance and Support Services

Team Growth:
We\'re welcoming two new talented developers to our team:
- Senior Frontend Developer specializing in Vue.js and React
- DevOps Engineer with expertise in AWS and containerization

Enhanced Capabilities:
- 24/7 support for critical applications
- Faster project delivery times
- More sophisticated technical solutions
- Expanded technology stack
- Better scalability for growing businesses

Our commitment remains the same: delivering high-quality, innovative web solutions that help our clients achieve their business goals. With our expanded team and services, we\'re better equipped than ever to tackle complex challenges and exceed expectations.

If you\'re considering a web development project or need technical consultation, we\'d love to discuss how our enhanced capabilities can benefit your business.',
                'excerpt' => 'Announcing the expansion of Darkheim Development Studio with new services, team members, and enhanced capabilities.',
                'image_url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Darkheim Team',
                'category' => 'announcements',
                'is_published' => true,
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(20),
                'views' => 298
            ],
            [
                'title' => 'Coming Soon: Advanced Project Management System',
                'slug' => 'coming-soon-advanced-project-management-system',
                'content' => 'We\'re currently developing an advanced project management system that will revolutionize how we collaborate with clients and manage development projects.

Planned Features:
- Real-time project progress tracking
- Client collaboration portal
- Integrated communication tools
- File sharing and version control
- Time tracking and billing integration
- Mobile app for on-the-go access
- Advanced reporting and analytics

This system will provide unprecedented transparency and collaboration opportunities for our clients. You\'ll be able to track project progress in real-time, communicate directly with our development team, and access all project-related documents in one centralized location.

Expected Benefits:
- Improved communication and transparency
- Faster feedback cycles
- Better project organization
- Enhanced client satisfaction
- Streamlined development process

We expect to launch this system in early 2026. Current and future clients will receive priority access to the beta version.

Stay tuned for more updates as we continue development on this exciting new tool!',
                'excerpt' => 'We\'re developing an advanced project management system that will revolutionize client collaboration and project transparency.',
                'image_url' => 'https://images.unsplash.com/photo-1611224923853-80b023f02d71?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Darkheim Team',
                'category' => 'general',
                'is_published' => false,
                'is_featured' => false,
                'published_at' => null,
                'views' => 0
            ]
        ];

        foreach ($news as $item) {
            News::create($item);
        }
    }
}
