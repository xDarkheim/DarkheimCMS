# DarkheimCMS

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-red.svg" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.2+-blue.svg" alt="PHP Version">
  <img src="https://img.shields.io/badge/Vue.js-3.x-green.svg" alt="Vue.js Version">
  <img src="https://img.shields.io/badge/Tests-49%20Unit%20%2B%2017%20Feature-brightgreen.svg" alt="Test Coverage">
  <img src="https://img.shields.io/badge/License-MIT-yellow.svg" alt="License">
</p>

## About DarkheimCMS

DarkheimCMS is a modern, full-stack content management system built with Laravel and Vue.js. It provides a comprehensive platform for managing portfolios, news, team members, careers, and company information with a powerful admin panel and RESTful API.

### Key Features

- 🎨 **Portfolio Management** - Showcase projects with categories, technologies, and detailed descriptions
- 📰 **News System** - Publish and manage news articles with multiple categories
- 👥 **Team Management** - Display team members with roles, skills, and social links
- 💼 **Career Portal** - Post job openings and manage applications
- 📧 **Contact System** - Handle general inquiries and job applications
- 🔐 **Admin Panel** - Full-featured administrative interface
- 🔄 **RESTful API** - Complete API for frontend integration
- 📱 **Responsive Design** - Mobile-first responsive interface
- 🧪 **Comprehensive Testing** - 66 tests covering all functionality

## Technology Stack

### Backend
- **Laravel 11** - PHP framework
- **MySQL** - Database
- **Laravel Sanctum** - API authentication
- **Intervention Image** - Image processing

### Frontend
- **Vue.js 3** - Frontend framework
- **Vite** - Build tool
- **Responsive CSS** - Mobile-first design

### Testing
- **PHPUnit** - Unit and Feature testing
- **49 Unit tests** - Model and component testing
- **17 Feature tests** - API and integration testing

## Installation

### Requirements
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL 5.7+

### Setup Instructions

1. **Clone the repository**
```bash
git clone https://github.com/xDarkheim/DarkheimCMS.git
cd DarkheimCMS
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node.js dependencies**
```bash
npm install
```

4. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure database in `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=darkheim_cms
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed
```

7. **Build assets**
```bash
npm run build
# or for development
npm run dev
```

8. **Create storage symlink**
```bash
php artisan storage:link
```

## Testing

The project includes comprehensive test coverage with PHPUnit.

### Setup Testing Environment
```bash
# Copy test configuration
cp phpunit.xml.example phpunit.xml

# Create test database
mysql -u root -p -e "CREATE DATABASE darkheim_cms_test;"

# Configure phpunit.xml with your test database credentials
```

### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run with coverage (requires Xdebug)
php artisan test --coverage
```

### Test Coverage
- **Unit Tests (49)**: Model testing, factories, configuration
- **Feature Tests (17)**: API endpoints, authentication, admin panel
- **Integration Tests**: Complete workflow testing
- **Security Tests**: Input validation and authorization
- **Performance Tests**: Database optimization and caching

## API Documentation

### Authentication
```bash
# Login
POST /api/login
{
    "email": "admin@example.com",
    "password": "password"
}

# Logout (authenticated)
POST /api/logout
Authorization: Bearer {token}
```

### Public Endpoints

#### Portfolios
```bash
GET /api/portfolios           # List all published portfolios
GET /api/portfolios/featured  # Get featured portfolios
GET /api/portfolios/{id}      # Get specific portfolio
GET /api/portfolios/categories # Get portfolio categories
```

#### News
```bash
GET /api/news                 # List all published news
GET /api/news/featured        # Get featured news
GET /api/news/latest          # Get latest news
GET /api/news/{slug}          # Get specific news article
```

#### Contact
```bash
POST /api/contact             # Submit contact form
```

#### Company Info
```bash
GET /api/company-info         # Get company information
GET /api/contact-info         # Get contact information
```

### Admin Endpoints (Authenticated)

#### Dashboard
```bash
GET /api/admin/dashboard      # Dashboard overview
GET /api/admin/dashboard/stats # Dashboard statistics
```

#### Portfolio Management
```bash
GET /api/admin/portfolios     # List all portfolios
POST /api/admin/portfolios    # Create portfolio
PUT /api/admin/portfolios/{id} # Update portfolio
DELETE /api/admin/portfolios/{id} # Delete portfolio
```

#### News Management
```bash
GET /api/admin/news           # List all news
POST /api/admin/news          # Create news article
PUT /api/admin/news/{id}      # Update news article
DELETE /api/admin/news/{id}   # Delete news article
```

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Admin/              # Admin panel controllers
│   ├── Api/                # API controllers
│   └── Auth/               # Authentication controllers
├── Models/                 # Eloquent models
│   ├── Portfolio.php
│   ├── News.php
│   ├── User.php
│   └── ...
└── Providers/             # Service providers

database/
├── factories/             # Model factories for testing
├── migrations/            # Database migrations
└── seeders/              # Database seeders

resources/
├── js/                   # Vue.js frontend
├── css/                  # Stylesheets
└── views/                # Blade templates

tests/
├── Feature/              # Feature tests (17)
│   ├── Admin/           # Admin functionality tests
│   └── Api/             # API endpoint tests
└── Unit/                # Unit tests (49)
    └── Models/          # Model tests
```

## Development

### Running in Development Mode
```bash
# Start Laravel development server
php artisan serve

# Start Vite development server (in another terminal)
npm run dev
```

### Code Quality Tools
```bash
# Run PHPStan (static analysis)
./vendor/bin/phpstan analyse

# Run Laravel Pint (code formatting)
./vendor/bin/pint

# Run tests with coverage
php artisan test --coverage
```

## Deployment

### Production Setup
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Build production assets
npm run build
```

### Environment Variables
Key environment variables for production:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- Database credentials
- Mail configuration
- File storage configuration

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Add tests for new functionality
5. Run the test suite (`php artisan test`)
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

## Security

If you discover any security-related issues, please email [your-email@domain.com] instead of using the issue tracker.

## License

DarkheimCMS is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

- **Documentation**: This README and inline code comments
- **Issues**: Use GitHub Issues for bug reports and feature requests
- **Testing**: Comprehensive test suite with 66 tests
- **Code Quality**: PHPStan static analysis and Laravel Pint formatting

---

Built with ❤️ using Laravel and Vue.js
