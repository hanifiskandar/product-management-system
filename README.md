# Product Management System

A full-stack product management application built as a technical assessment using Laravel 13, Vue 3, Inertia.js, Tailwind CSS, and PrimeVue.

## Features

- **Products** — Create, edit, delete products with name, quantity, and category
- **Categories** — Manage product categories with create, edit, and search
- **Search & Filter** — Search products by name, filter by category; search categories by name
- **Sortable Table** — Sort products by name, quantity, or date; default sorted by newest
- **Pagination** — Paginated listing for both products and categories (10 per page)
- **Stats Bar** — At-a-glance counts for total products, total categories, and low stock items
- **Quantity Badges** — Color-coded badges: green (>10), yellow (5–10), red (<5)
- **Soft Deletes** — Deleted records are preserved in the database but hidden from views
- **Confirmation Dialogs** — Delete confirmation before removing products
- **Flash Notifications** — Success/error toast messages after actions
- **Responsive Design** — Mobile-friendly layout with Tailwind CSS

## Tech Stack

- **Backend**: Laravel 13, PHP 8.4, SQLite
- **Frontend**: Vue 3 (Composition API + `<script setup>`), Inertia.js v3, Tailwind CSS v4, PrimeVue (Aura theme)
- **Routing**: Laravel Wayfinder (type-safe route functions)
- **Testing**: Pest PHP v4

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+
- npm

## Setup Instructions

```bash
# 1. Clone the repository
git clone <repository-url>
cd product-management-system

# 2. Install PHP dependencies
composer install

# 3. Set up environment
cp .env.example .env
php artisan key:generate

# 4. Run migrations and seed the database
php artisan migrate --seed

# 5. Install JavaScript dependencies
npm install

# 6. Build frontend assets (or use dev server)
npm run build
# OR for development with hot-reloading:
# npm run dev

# 7. Start the application
php artisan serve
```

Open your browser at **http://localhost:8000**.

## Development

Run the full development stack (server + queue + Vite) with:

```bash
composer run dev
```

### Running Tests

```bash
php artisan test --compact
```

### Code Style

PHP formatting (Laravel Pint):

```bash
vendor/bin/pint
```

JavaScript/Vue linting:

```bash
npm run lint
npm run format
```
