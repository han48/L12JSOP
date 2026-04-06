# Laravel Admin

This is the admin project for the Laravel application, built with the Laravel framework and Orchid admin panel. The project uses Vue.js for the user interface via Inertia.js, and Tailwind CSS for styling.

## System Requirements

- PHP ^8.2
- Composer
- Node.js and npm
- Docker (optional, using Laravel Sail)

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd Laravel-admin
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment configuration

Copy the `.env.example` file to `.env` and configure the necessary environment variables:

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Run migrations and seed the database

```bash
php artisan migrate
php artisan db:seed
```

### 5. Create symbolic link for storage

```bash
php artisan storage:link
```

### 6. Create Orchid admin account

```bash
php artisan orchid:admin
```

## Running the Project

### Running in development environment

Use the `dev` script in composer to run the server, queue, logs, and Vite simultaneously:

```bash
composer run dev
```

Or run individually:

```bash
# Run Laravel server
php artisan serve

# Run queue worker
php artisan queue:listen --tries=1

# Run logs
php artisan pail --timeout=0

# Run Vite (frontend)
npm run dev
```

### Using Laravel Sail (Docker)

If you are using Docker:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run dev
```

## Build for Production

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Useful Scripts

### Clean script (clean.sh)

This script will reinstall all dependencies, clear caches, perform a fresh migration with seed, and run tests:

```bash
./clean.sh
```

### Build script (build.sh)

This script is similar to clean.sh but only migrates (no fresh):

```bash
./build.sh
```

### Run tests

```bash
composer run test
# or
php artisan test
```

### Generate ER Diagram

```bash
php artisan generate:erd erd.jpeg --format=jpeg
```

## Console Commands

This project includes custom Artisan commands to streamline development and management tasks.

### Management Create

Creates a complete data management system including model, migration, Orchid screens, layouts, helpers, API controllers, routes, menus, and permissions.

```bash
php artisan management:create {name}
```

Example:
```bash
php artisan management:create Product
```

This command will:
- Create the Product model and migration
- Generate Orchid list and edit screens
- Create API controller and routes
- Add menu items and permissions
- Optionally assign permissions to users

### User View Create

Creates user-facing Vue.js views for listing and showing data.

```bash
php artisan user:view {name}
```

Example:
```bash
php artisan user:view Product
```

This command will:
- Create Vue.js components for list and show views
- Add routes to web.php
- Support both grid and list layout types

### Send Notification

Sends dashboard notifications to users via Orchid's notification system.

```bash
php artisan notification:send {title} --user_ids=1,2 --message="Welcome message" --action="/" --type="info"
```

Parameters:
- `title`: Notification title (required)
- `--user_ids`: Comma-separated user IDs (optional, sends to all users if not specified)
- `--message`: Notification message (optional, defaults to title)
- `--action`: Action URL (optional)
- `--type`: Notification type - info, success, warning, error (optional, defaults to info)

Examples:
```bash
# Send to all users
php artisan notification:send "Welcome" --message="Welcome to the system"

# Send to specific users
php artisan notification:send "Update Available" --user_ids=1,2,3 --message="New version released" --action="/updates" --type="success"
```

## Project Structure

- `app/` - Main Laravel application code
- `resources/` - Views, CSS, JS
- `routes/` - Route definitions
- `database/` - Migrations, seeders, factories
- `config/` - Laravel configuration
- `public/` - Static assets
- `tests/` - Unit and feature tests

## Technologies Used

### Backend
- **Laravel 12** - PHP framework
- **Orchid Platform** - Admin panel
- **Inertia.js** - SPA framework
- **Jetstream** - Authentication scaffolding
- **Sanctum** - API authentication
- **Telescope** - Debugging tool
- **Horizon** - Queue dashboard

### Frontend
- **Vue.js 3** - JavaScript framework
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Build tool
- **Heroicons** - Icon library

### Database & Storage
- **AWS S3** - File storage
- **Excel** - Import/Export functionality

### Development Tools
- **Laravel Sail** - Docker development environment
- **Laravel Dusk** - Browser testing
- **Pint** - Code style fixer
- **ER Diagram Generator** - Database visualization

## Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request
