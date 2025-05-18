# 🚀 Laravel & Vue Full-Stack Integration Challenge

[![PHP](https://img.shields.io/badge/PHP-^8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-^12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![Vue.js](https://img.shields.io/badge/Vue.js-^3.3.13-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org/)
[![TypeScript](https://img.shields.io/badge/TypeScript-^5.8.3-3178C6?style=for-the-badge&logo=typescript&logoColor=white)](https://www.typescriptlang.org/)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-^2.0-6675e0?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com/)
[![Pinia](https://img.shields.io/badge/Pinia-^3.0.2-ffd859?style=for-the-badge&logo=pinia&logoColor=black)](https://pinia.vuejs.org/)
[![Pest](https://img.shields.io/badge/Pest-^3.8-f9322c?style=for-the-badge&logo=php&logoColor=white)](https://pestphp.com/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-336791?style=for-the-badge&logo=postgresql&logoColor=white)](https://www.postgresql.org/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-^3.4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)

## 📋 Overview

This project is a Full-Stack integration challenge using Laravel and Vue.js, with the goal of demonstrating the ability to build a cohesive and modern application. The development is guided by two main user stories:

1.  **STORY-001**: Implementation of a resource listing ("Products") with filterable API endpoints, pagination, and reactive UI updates.
2.  **STORY-002**: Implementation of a form ("Registrations") with real-time validation on the frontend and integration with robust backend validation, followed by data persistence.

The focus is on efficient integration between the Laravel backend and Vue.js frontend through Inertia.js, following best practices for development, performance, and maintainability.

## 🛠️ Main Technology Stack

The technology selection aims for productivity, performance, and maintainability, using modern and efficient versions of each tool:

### Backend
* 🖥️ **PHP**: `^8.2`
* 🔧 **Laravel Framework**: `^12.0`
* 🌐 **Application Server**: (Implicit, via `php artisan serve` or Sail)

### Frontend
* ⚡ **Vue.js**: `^3.3.13`
* 📝 **TypeScript**: `^5.8.3`
* 🔄 **Vite**: `^6.2.4`
* 🎨 **Tailwind CSS**: `^3.4.0`

### Backend-Frontend Integration
* 🔌 **Inertia.js (Laravel Adapter)**: `^2.0`
* 🔌 **Inertia.js (Vue3 Adapter)**: `^2.0`
* 🛣️ **Ziggy (Laravel Routes in JS)**: `^2.5` (Laravel), `^2.5.2` (JS)

### Database
* 🗄️ **PostgreSQL**: Version 16 (as specified in the requirements document)

### State Management (Frontend)
* 📊 **Pinia**: `^3.0.2` ( - in `package.json` it's `^3.0.2`, the document mentions Pinia 2)

### Testing
* **Backend (PHP)**:
    * 🧪 **Pest**: `^3.8`
    * 🧪 **Pest Plugin Laravel**: `^3.2`
* **Frontend (JS/TS)**:
    * 🧪 **Vitest**: `^3.1.3`
    * 🧪 **Vue Test Utils**: `^2.4.6`
    * 🧪 **Vue TSC (Type Checking)**: `^2.2.10`

### Other Key Tools
* 🛡️ **Laravel Breeze**: `^2.3` (for initial scaffolding, including SSR with Vue)
* 🔐 **Laravel Sanctum**: `^4.0` (for API authentication, if needed)
* 🌐 **Axios**: `^1.8.2` (for HTTP requests)
* ✅ **Vee-Validate**: `4.15.0` (for form validation in Vue)
* 📋 **Yup**: `^1.6.1` (for validation schemas with Vee-Validate)
* 🧹 **Laravel Pint**: `^1.13` (for PHP code formatting)
* 📦 **Composer**: For PHP dependency management.
* 📦 **NPM/Yarn**: For JavaScript dependency management.

## ✨ Main Features

### STORY-001: Resource Listing with Filterable API Endpoints
* 📋 Display of a paginated list of "Products".
* 🔍 Allows the user to filter results based on at least three parameters (e.g., name, category, status).
* ⚡ Filter changes update the list reactively, without reloading the page.
* 🔖 Navigation between result pages preserves applied filters.
* 🔄 Loading indicators and messages for "no results found".
* 🔗 API Endpoint: `GET /api/products` (or similar, as defined in Inertia routes).

### STORY-002: Real-Time Form Validation with Backend Integration
* 📝 Implementation of a dynamic form (e.g., "Registrations").
* ✅ Real-time validation of fields on the client side (when typing/changing focus).
* 🛡️ Final and definitive validation on the backend when submitting the form.
* ⚠️ Contextual display of error messages (frontend and backend).
* 💾 Persistence of data in the database on success, with user feedback.
* 🔗 API Endpoint: `POST /api/registrations` (or similar, as defined in Inertia routes).

## 📋 Prerequisites

* ✅ PHP >= 8.2
* ✅ Composer
* ✅ Node.js (recommended LTS version)
* ✅ NPM (v9+) or Yarn
* ✅ PostgreSQL >= 14 (ideally 16, as specified)

## 🔧 Development Environment Setup

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd <project-directory-name>
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Install Node.js dependencies:**
    ```bash
    npm install
    # or
    # yarn install
    ```

4.  **Configure Environment Variables:**
    * Copy the `.env.example` file to `.env`:
        ```bash
        cp .env.example .env
        ```
    * Edit the `.env` file and configure the variables, especially the database connection ones (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`):
        ```ini
        DB_CONNECTION=pgsql
        DB_HOST=127.0.0.1
        DB_PORT=5432
        DB_DATABASE=your_database
        DB_USERNAME=your_username
        DB_PASSWORD=your_password

        APP_NAME="Laravel Inertia Vue Challenge"
        APP_ENV=local
        APP_KEY=
        APP_DEBUG=true
        APP_URL=http://localhost:8000 # Adjust if needed
        ```

5.  **Generate Laravel Application Key:**
    ```bash
    php artisan key:generate
    ```

6.  **Configure Database:**
    * Start the Postgres container and make sure your PostgreSQL server is running and the database specified in `.env` exists.
  * ```bash
    docker-compose up -d
    ```

7.  **Run Migrations and Seeders:**
    * The migrations will create the `categories`, `products`, and `registrations` tables.
    * The seeders will populate the tables with test data (e.g., 5-10 categories, 50-100 products).
    ```bash
    php artisan migrate --seed
    ```

## ▶️ Running the Application

The project uses Vite for the frontend. You can run the Laravel and Vite development servers simultaneously.

* **Option 1: Using the `dev` script from `composer.json` (recommended):**
    This script uses `concurrently` to start the PHP server, the queue listener, pail (logs), and the Vite development server.
    ```bash
    composer run dev
    ```
    This will run: `php artisan serve`, `php artisan queue:listen`, `php artisan pail`, and `npm run dev`.

* **Option 2: Manually in separate terminals:**
    * Terminal 1: Laravel PHP Server
        ```bash
        php artisan serve
        ```
    * Terminal 2: Vite Development Server (for HMR and asset compilation)
        ```bash
        npm run dev
        ```

After starting the servers, the application will generally be accessible at `http://localhost:8000` (or the URL configured/returned by `php artisan serve`).

## 🧪 Running Tests

### Backend Tests (Pest)
To run Laravel feature and unit tests with Pest:
```bash
php artisan test
# or
composer run test
````
Feature test `RegistrationControllerTest`
```
  ✓ Registration Creation (Store Endpoint) → it should create a registration wit… 0.14s
  ✓ Registration Creation (Store Endpoint) → it should fail validation if name i… 0.01s
  ✓ Registration Creation (Store Endpoint) → it should fail validation if email…  0.01s
  ✓ Registration Creation (Store Endpoint) → it should fail validation if email…  0.01s
  ✓ Registration Creation (Store Endpoint) → it should fail validation if email…  0.02s
  ✓ Registration Creation (Store Endpoint) → it should fail validation if passwo… 0.01s
  ✓ Registration Creation (Store Endpoint) → it should fail validation if passwo… 0.01s
  ✓ Email Validation Endpoint (/api/validate-email) → it should return exists tr… 0.01s
  ✓ Email Validation Endpoint (/api/validate-email) → it should return exists fa… 0.01s
  ✓ Email Validation Endpoint (/api/validate-email) → it should fail validation…  0.01s
  ✓ Email Validation Endpoint (/api/validate-email) → it should fail validation…  0.01s
```

### Frontend Tests
To run frontend tests with Vitest and Vue Test Utils:
```bash
npm run test
# or
yarn test
```
Cases:
```
 ✓ tests/Js/Components/Products/ProductFilters.spec.ts (2 tests) 17ms
 ✓ tests/Js/Pages/Products/Index.spec.ts (2 tests) 32ms
```
