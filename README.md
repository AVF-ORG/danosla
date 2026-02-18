# DANOSLA

**DANOSLA** is a transport management platform for **customers**, **transporters**, **firm bids**, and **operations analytics**.

Built with **Laravel 12**, **Tailwind CSS v4**, **Alpine.js**, and a clean, modular architecture. It is designed for building fast, scalable transport management solutions.

## ✨ Key Features

* 🚀 **Laravel 12 Core** - Built on the latest Laravel release with improved routing, security, and Blade templating
* 🎨 **Tailwind CSS v4** - Utility-first styling for rapid, consistent UI development
* ⚡ **Alpine.js Interactivity** - Lightweight reactivity without a heavy JavaScript framework
* 📦 **Vite Build System** - Fast dev server, instant HMR, and optimized production builds
* 📱 **Fully Responsive Layouts** - Smooth, mobile-first design that adapts across all screen sizes
* 🌙 **Built-in Dark Mode** - Ready-to-use modern dark theme for better usability and aesthetics
* 📊 **Advanced UI Components** - Charts, data tables, forms, calendars, modals, and reusable blocks
* � **Localization** - Database-driven translation system for multi-language support

## 📋 Requirements
To set up DANOSLA, make sure your environment includes:

* **PHP 8.2+**
* **Composer** (PHP dependency manager)
* **Node.js 18+** and **npm** (for compiling frontend assets)
* **Database** - Works with SQLite (default), MySQL, or PostgreSQL

### Check Your Environment

Verify your installations:

```bash
php -v
composer -V
node -v
npm -v
```

## 🚀 Quick Start Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/avf-dashboard.git
cd avf-dashboard
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

This command will install all Laravel dependencies defined in `composer.json`.

### Step 3: Install Node.js Dependencies

```bash
npm install
```

Or if you prefer yarn or pnpm:

```bash
# Using yarn
yarn install

# Using pnpm
pnpm install
```

### Step 4: Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

**For Windows users:**

```bash
copy .env.example .env
```

**Or create it programmatically:**

```bash
php -r "file_exists('.env') || copy('.env.example', '.env');"
```

### Step 5: Generate Application Key

```bash
php artisan key:generate
```

This creates a unique encryption key for your application.

### Step 6: Configure Database

#### Option A: Using MySQL/PostgreSQL

Update your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=danosla_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Create the database:

```bash
# MySQL
mysql -u root -p -e "CREATE DATABASE danosla_db;"

# PostgreSQL
createdb danosla_db
```

Run migrations:

```bash
php artisan migrate
```

### Step 7: (Optional) Seed the Database

If you want sample data:

```bash
php artisan db:seed
```

### Step 8: Storage Link

Create a symbolic link for file storage:

```bash
php artisan storage:link
```

## 🏃 Running the Application

### Development Mode (Recommended)

The easiest way to start development is using the built-in script:

```bash
composer run dev
```

This single command starts:
- ✅ Laravel development server (http://localhost:8000)
- ✅ Vite dev server for hot module reloading
- ✅ Queue worker for background jobs
- ✅ Log monitoring

**Access your application at:** [http://localhost:8000](http://localhost:8000)

### Manual Development Setup

If you prefer to run services individually in separate terminal windows:

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Frontend Assets:**
```bash
npm run dev
```

### Building for Production

#### Build Frontend Assets

```bash
npm run build
```

#### Optimize Laravel

```bash
# Clear and cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

#### Production Environment

Update your `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```


## 🧪 Testing

Run the test suite using Pest:

```bash
composer run test
```

Or manually:

```bash
php artisan test
```

## � Project Structure

```
avf-dashboard/
├── app/                    # Application logic
│   ├── Http/              # Controllers, Middleware, Requests
│   ├── Models/            # Eloquent models
│   └── Providers/         # Service providers
├── bootstrap/             # Framework bootstrap files
├── config/                # Configuration files
├── database/              # Migrations, seeders, factories
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── public/                # Public assets (entry point)
│   ├── build/            # Compiled assets (generated)
│   └── index.php         # Application entry point
├── resources/             # Views and raw assets
│   ├── css/              # Stylesheets (Tailwind)
│   ├── js/               # JavaScript files (Alpine.js)
│   └── views/            # Blade templates
├── routes/                # Route definitions
│   ├── web.php           # Web routes
│   ├── api.php           # API routes
│   └── console.php       # Console routes
├── storage/               # Logs, cache, uploads
│   ├── app/
│   ├── framework/
│   └── logs/
├── tests/                 # Pest test files
│   ├── Feature/
│   └── Unit/
├── .env.example           # Example environment file
├── artisan                # Artisan CLI
├── composer.json          # PHP dependencies
├── package.json           # Node dependencies
├── vite.config.js         # Vite configuration
└── tailwind.config.js     # Tailwind configuration
```

## License

This project is licensed under the MIT License.
