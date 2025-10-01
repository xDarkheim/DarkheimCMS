<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(18),
                'views' => 298
            ],
            [
                'title' => 'Web Security Fundamentals: Protecting Your Business Online',
                'slug' => 'web-security-fundamentals-protecting-business-online',
                'content' => 'In an era of increasing cyber threats, web security has become more critical than ever. At Darkheim Development Studio, we prioritize security in every project we undertake.

Essential Security Measures:
- SSL certificates for encrypted data transmission
- Regular security updates and patches
- Strong authentication and authorization systems
- Input validation and sanitization
- Secure coding practices
- Regular security audits and penetration testing

Common vulnerabilities we protect against:
- SQL injection attacks
- Cross-site scripting (XSS)
- Cross-site request forgery (CSRF)
- Session hijacking
- Brute force attacks
- Data breaches

Our security implementation process:
1. Security assessment during project planning
2. Secure architecture design
3. Implementation with security best practices
4. Regular security testing throughout development
5. Post-launch security monitoring and maintenance

For businesses, investing in web security means protecting customer data, maintaining trust, and avoiding costly security breaches. We help our clients understand and implement the security measures that make sense for their specific needs and budget.',
                'excerpt' => 'Learn about essential web security measures and how we help businesses protect their online presence from cyber threats.',
                'image_url' => 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Security Team',
                'category' => 'tutorials',
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(22),
                'views' => 234
            ],
            [
                'title' => 'Why We Choose Vue.js for Modern Web Applications',
                'slug' => 'why-we-choose-vuejs-modern-web-applications',
                'content' => 'Vue.js has become our frontend framework of choice for creating interactive, responsive web applications. Here\'s why we believe it\'s the best choice for most projects.

Advantages of Vue.js:
- Gentle learning curve for developers
- Excellent documentation and community support
- Progressive framework that scales with project needs
- Great performance with virtual DOM
- Strong ecosystem with Vue Router and Vuex
- Excellent developer tools and debugging experience

When we use Vue.js:
- Interactive dashboards and admin panels
- E-commerce websites with dynamic product catalogs
- Real-time applications with live updates
- Single-page applications (SPAs)
- Progressive web applications (PWAs)

Integration with Laravel:
Vue.js pairs perfectly with our Laravel backend, allowing us to build robust full-stack applications. Laravel provides the API endpoints while Vue.js handles the interactive frontend experience.

The combination of Laravel + Vue.js enables us to deliver modern web applications that are both powerful on the backend and engaging on the frontend.',
                'excerpt' => 'Discover why Vue.js has become our frontend framework of choice and how it integrates perfectly with Laravel for modern web development.',
                'image_url' => 'https://images.unsplash.com/photo-1587620962725-abab7fe55159?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Frontend Team',
                'category' => 'development',
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(25),
                'views' => 189
            ],
            [
                'title' => 'Building Responsive Design: Mobile-First Approach in 2025',
                'slug' => 'building-responsive-design-mobile-first-approach-2025',
                'content' => 'With mobile traffic accounting for over 60% of web browsing, mobile-first design isn\'t just a best practice—it\'s essential for business success.

Our mobile-first methodology:
1. Start with mobile layout and functionality
2. Design for touch interactions from the beginning
3. Optimize for smaller screens and slower connections
4. Progressive enhancement for larger screens
5. Test across multiple devices and browsers

Key considerations for mobile-first design:
- Fast loading times (under 3 seconds)
- Thumb-friendly navigation and buttons
- Readable text without zooming
- Optimized images and media
- Simplified navigation menus
- Touch-friendly form inputs

Tools and techniques we use:
- CSS Grid and Flexbox for flexible layouts
- Responsive images with srcset
- CSS media queries for different screen sizes
- Performance optimization for mobile networks
- Progressive web app features when appropriate

The result is websites that work beautifully on any device, providing an optimal user experience that converts visitors into customers.',
                'excerpt' => 'Learn about our mobile-first design methodology and why it\'s essential for creating successful websites in 2025.',
                'image_url' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Design Team',
                'category' => 'development',
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(28),
                'views' => 167
            ],
            [
                'title' => 'Database Optimization Tips for Better Web Performance',
                'slug' => 'database-optimization-tips-better-web-performance',
                'content' => 'Database performance is often the bottleneck in web applications. Here are our proven strategies for optimizing database performance in Laravel applications.

Essential optimization techniques:
- Proper indexing strategy for frequently queried columns
- Query optimization and avoiding N+1 problems
- Database connection pooling and management
- Caching frequently accessed data
- Using appropriate data types and table structures

Laravel-specific optimizations:
- Eager loading relationships to reduce queries
- Using database transactions for data integrity
- Implementing query scopes for reusable logic
- Database seeders and factories for development data
- Migration best practices for schema changes

Monitoring and analysis:
- Laravel Telescope for query analysis
- Database query logging and profiling
- Performance monitoring tools
- Regular database maintenance and optimization
- Backup and recovery strategies

Tools we use:
- Laravel Eloquent ORM for database interactions
- MySQL/MariaDB for reliable data storage
- Redis for caching and session storage
- Database migration tools for version control

These optimization strategies have helped us achieve significant performance improvements in client projects, with some seeing query times reduced by up to 70%.',
                'excerpt' => 'Discover our proven database optimization strategies that can significantly improve your web application\'s performance.',
                'image_url' => 'https://images.unsplash.com/photo-1518186285589-2f7649de83e0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
                'author' => 'Backend Team',
                'category' => 'tutorials',
                'is_published' => true,
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(32),
                'views' => 156
            ]
        ];

        foreach ($news as $item) {
            News::create($item);
        }
    }
}
