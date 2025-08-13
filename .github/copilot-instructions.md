# AI Agent Instructions for katauser-fs-lara-vue

## Project Overview

A Laravel + Vue.js application with Inertia.js for server-side rendering. The project implements a feedback and roadmap management system with user authentication, organizations, and subscription plans.

## Architecture

### Backend (Laravel)

- **Authentication**: Standard Laravel authentication with email/password
- **Models**: Located in `app/Models/` following Laravel Eloquent patterns
    - Key models: User, Organization, FeedbackBoard, FeedbackPost, RoadmapItem
    - Uses traits: `Auditable`, `TracksChanges` for model behavior
- **Controllers**: Organized in `app/Http/Controllers/` with dedicated auth and settings controllers
- **Routes**: Split into multiple files:
    - `routes/web.php`: Main web routes
    - `routes/auth.php`: Authentication routes
    - `routes/settings.php`: User settings routes

### Frontend (Vue 3 + Inertia.js)

- **Pages**: Inertia.js pages in `resources/js/pages/`
- **Components**: Reusable Vue components in `resources/js/components/`
- **Layouts**: Page layouts in `resources/js/layouts/`
    - `AuthLayout.vue`: Authentication pages
    - Other layouts for different sections
- **State Management**: Uses Inertia shared data for global state

## Key Development Patterns

### Authentication & Authorization

- Uses Laravel's built-in authentication
- Protected routes use `auth` middleware
- User roles defined in `app/Enums/UserRole.php`
- Rate limiting on auth endpoints (e.g., `throttle:6,1` on password updates)

### Database Patterns

- Migrations in `database/migrations/`
- Uses Laravel's migration system for schema changes
- Factories for testing in `database/factories/`

### Testing

- Uses Pest PHP testing framework
- Feature tests in `tests/Feature/`
- Auth tests demonstrate key flows:
    ```php
    test('users can authenticate using the login screen', function () {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $this->assertAuthenticated();
    });
    ```

## Development Workflow

### Setup

1. Install PHP dependencies: `composer install`
2. Install Node.js dependencies: `npm install`
3. Copy .env.example: `cp .env.example .env`
4. Generate app key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`

### Local Development

- Run backend: `php artisan serve`
- Run frontend: `npm run dev`
- Run tests: `./vendor/bin/pest`

## Common Patterns to Follow

### Adding New Features

1. Create migrations for any DB changes
2. Add/update models with relationships
3. Create/update controller actions
4. Add routes in appropriate route file
5. Create Inertia pages and components
6. Add feature tests

### Code Style

- Follow Laravel conventions for PHP
- Use TypeScript for Vue components
- Organize components by feature/domain
- Use Pest for testing with descriptive test names

### Common Gotchas

- Always use `route()` helper with `absolute: false` for Inertia redirects
- Check auth status with `$request->user()` in middleware
- Use enums for status/type fields (see `app/Enums/`)
- Handle file uploads through `storage/app/public/`
