
# Product Catalog App

## Features
- Create products (web, CLI)
- List products with sorting/filtering by category (web)
- Category hierarchy (parent/child)
- Repository & service pattern for clean architecture
- Automated tests for product creation

## Quick Start (Automated)
Run the entire project with one command:
```bash
./run-project.sh
```
This script will:
- Install all dependencies (backend + frontend)
- Set up environment and database
- Run migrations and seeders
- Build frontend assets
- Run tests to verify everything works
- Optionally start the development server

## Manual Setup
1. Clone the repo and install dependencies:
	```bash
	composer install
	npm install
	```
2. Copy `.env.example` to `.env` and set DB credentials.
3. Run migrations:
	```bash
	php artisan migrate
	```
4. Build frontend assets:
	```bash
	npm run dev
	```

## CLI Product Creation
Create a product from the command line:
```bash
php artisan product:create
```
Follow the prompts for product details and category assignment.

## Automated Tests
Run feature tests:
```bash
php artisan test --filter=ProductCreationTest
```


## Build & Setup Automation
**One-command setup**: `./run-project.sh`

**Individual commands**:
- Backend: `composer install && php artisan migrate`
- Frontend: `npm install && npm run dev`
- Run tests: `php artisan test`

## Architecture
- Eloquent models: `Product`, `Category`
- Repository layer: wraps all Eloquent queries
- Service layer: business logic and validation
- API controllers: use service layer only
- Vue.js frontend (see web UI branch)

## Contributing
Follow PSR standards and commit progressively for clean history.
