#!/bin/bash

# Simple Project Runner - Product Catalog Backend Challenge
# This script sets up and runs the entire project

set -e

echo "🚀 Starting Product Catalog Backend Challenge..."

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

print_step() {
    echo -e "${BLUE}▶${NC} $1"
}

print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_info() {
    echo -e "${YELLOW}ℹ${NC} $1"
}

# Check requirements
print_step "Checking requirements..."
command -v php >/dev/null 2>&1 || { echo "PHP is required but not installed."; exit 1; }
command -v composer >/dev/null 2>&1 || { echo "Composer is required but not installed."; exit 1; }
command -v npm >/dev/null 2>&1 || { echo "npm is required but not installed."; exit 1; }
print_success "All requirements met"

# Install backend dependencies
print_step "Installing backend dependencies..."
composer install --quiet
print_success "Backend dependencies installed"

# Install frontend dependencies  
print_step "Installing frontend dependencies..."
npm install --silent
print_success "Frontend dependencies installed"

# Setup environment
print_step "Setting up environment..."
if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate --quiet
    print_success "Environment configured"
else
    print_info "Environment already exists"
fi

# Database setup
print_step "Setting up database..."
php artisan migrate --force --quiet
php artisan db:seed --quiet
print_success "Database ready"

# Build frontend assets
print_step "Building frontend assets..."
npm run dev --silent
print_success "Frontend assets built"

# Create storage link
print_step "Setting up storage..."
php artisan storage:link --quiet 2>/dev/null || true
print_success "Storage configured"

# Run tests
print_step "Running tests..."
php artisan test
print_success "All tests passed"

echo ""
print_success "🎉 Project is ready!"
echo ""
print_info "Available commands:"
echo "  • Start server:     php artisan serve"
echo "  • Create product:   php artisan product:create"
echo "  • Run tests:        php artisan test"
echo "  • Frontend watch:   npm run watch"
echo ""
print_info "API Endpoints:"
echo "  • GET  /api/products     - List products"
echo "  • POST /api/products     - Create product"
echo "  • GET  /api/categories   - List categories"
echo ""
print_info "Access the web UI at: http://localhost:8000"

# Optionally start the server
read -p "Start the development server now? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    print_step "Starting development server..."
    php artisan serve
fi