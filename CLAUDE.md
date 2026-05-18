# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Setup
```bash
# Install dependencies
composer install
npm install

# Copy environment file and generate key
cp .env.example .env
php artisan key:generate

# Create database and run migrations
touch database/database.sqlite
php artisan migrate

# Start development servers
npm run dev  # Vite dev server
php artisan serve  # Laravel dev server
```

### Testing
```bash
# Run all tests
php artisan test

# Run tests with coverage
php artisan test --coverage

# Run a specific test file
php artisan test tests/Feature/UserTest.php

# Run a specific test method
php artisan test --filter test_user_can_login
```

### Code Quality
```bash
# Run Pint (Laravel PHP code style fixer)
vendor/bin/pint

# Pint with diff only (check for issues)
vendor/bin/pint --test

# Run Pest with verbose output
php artisan test --verbose
```

### Database
```bash
# Refresh database (drop and recreate all tables)
php artisan migrate:fresh

# Seed database
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=UserSeeder

# Rollback last migration
php artisan migrate:rollback
```

## Code Architecture

### Application Structure
- **app/** - Core application code
  - **Models/** - Eloquent models (User, Department, Task, etc.)
  - **Controllers/** - HTTP controllers (both web and API)
  - **Traits/** - Reusable code traits (RecycleBinTrait)
  - **Modules/** - Feature-specific code organization
  - **Providers/** - Service providers
  - **Http/Resources/** - API resources for response formatting
  - **Http/Requests/** - Form request validation classes

### Key Features
1. **Authentication** - Laravel Sanctum for API token authentication
2. **Soft Delete/Recycle Bin** - Custom RecycleBinTrait implements soft delete with restore/force delete functionality
3. **API Resources** - Transforms model data for JSON responses
4. **Modular Organization** - Users module contains specific actions (image upload/delete)

### Important Files
- **routes/api.php** - Defines all API endpoints
- **app/Models/User.php** - Primary user model with relationships
- **app/Traits/RecycleBinTrait.php** - Soft delete implementation
- **composer.json** - Dependencies and scripts
- **phpunit.xml** - Testing configuration

### Common Patterns
- Models use Laravel attributes (`#[Fillable]`, `#[Hidden]`)
- Controllers follow RESTful conventions
- API routes use route model binding
- Request validation handled by Form Request classes
- Testing follows Pest conventions

## Database Schema
- Users table with role, department_id, salary, image fields
- Departments table for organizational structure
- Tasks table assigned to users
- SalaryRecords table for compensation history
- PerformanceReviews table for evaluations
- RecycleBin table tracks soft-deleted records across models

## Environment
- PHP ^8.3 required
- Laravel ^13.0 framework
- SQLite default database (database/database.sqlite)
- Sanctum for API authentication
- Pest for testing
- Vite for frontend assets