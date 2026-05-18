# Workflow Pro Backend

A Laravel-based backend API for a workflow management system. This application provides RESTful APIs for managing users, departments, tasks, salary records, performance reviews, and includes a soft delete/recycle bin feature.

## Features

- **Authentication**: Laravel Sanctum for API token authentication
- **User Management**: CRUD operations with role-based access
- **Department Management**: Organizational structure
- **Task Management**: Assign tasks to users
- **Salary Records**: Track compensation history
- **Performance Reviews**: Employee evaluations
- **Soft Delete/Recycle Bin**: Custom trait for safe deletion with restore/force delete
- **API Resources**: Formatted JSON responses
- **Modular Organization**: Feature-specific code structure
- **Form Request Validation**: Dedicated request classes for input validation
- **Testing**: Comprehensive test suite with Pest

## Installation

Follow these steps to set up the project locally:

### Prerequisites
- PHP ^8.3
- Composer
- Node.js & npm (for Vite)
- SQLite (or other database)

### Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/moh892002/workflow-pro-backend
   cd workflow-pro-backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

5. **(Optional) Seed database**
   ```bash
   php artisan db:seed
   ```

### Development Servers

Start the development servers:

```bash
# Vite dev server (for frontend assets, if any)
npm run dev

# Laravel dev server
php artisan serve
```

The API will be available at `http://localhost:8000`.

## Testing

Run the test suite:

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

## Code Quality

```bash
# Run Pint (Laravel PHP code style fixer)
vendor/bin/pint

# Pint with diff only (check for issues)
vendor/bin/pint --test

# Run Pest with verbose output
php artisan test --verbose
```

## Database Schema

- **Users**: id, name, email, role, department_id, salary, image, timestamps, soft deletes
- **Departments**: id, name, timestamps
- **Tasks**: id, user_id, title, description, status, timestamps
- **Salary Records**: id, user_id, amount, effective_date, timestamps
- **Performance Reviews**: id, user_id, reviewer_id, rating, comments, timestamps
- **Recycle Bin**: tracks soft-deleted records across models

## API Endpoints

Refer to `routes/api.php` for all available endpoints. The API uses route model binding and returns JSON responses via API resources.

## Directory Structure

- `app/` - Core application code
  - `Models/` - Eloquent models
  - `Controllers/` - HTTP controllers
  - `Traits/` - Reusable code (e.g., RecycleBinTrait)
  - `Modules/` - Feature-specific organization
  - `Providers/` - Service providers
  - `Http/Resources/` - API resources
  - `Http/Requests/` - Form request validation
- `routes/` - API route definitions
- `database/` - Migrations, seeders, SQLite database
- `tests/` - Feature and unit tests

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).