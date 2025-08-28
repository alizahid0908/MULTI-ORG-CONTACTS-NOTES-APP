# Multi-Organization Contacts & Notes App

A Laravel 12 + Inertia.js + React TypeScript application for managing contacts and notes across multiple organizations with strict organization scoping and role-based permissions.

## Features

- **Multi-Organization Support**: Users can belong to multiple organizations with different roles
- **Strict Organization Scoping**: All data is automatically scoped to the current organization
- **Role-Based Permissions**: Admin and Member roles with different capabilities
- **Contact Management**: Full CRUD operations with avatar upload and duplicate detection
- **Notes System**: Users can add notes to contacts with proper ownership tracking
- **Custom Fields**: Up to 5 key-value pairs per contact
- **Duplicate Detection**: Case-insensitive email duplicate detection with exact error responses
- **Cross-Org Isolation**: Complete data isolation between organizations

## Tech Stack

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Inertia.js, React, TypeScript
- **Database**: SQLite (default) / MySQL (optional)
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission
- **Styling**: Tailwind CSS, shadcn/ui components
- **Testing**: Pest PHP

## Setup Instructions

### Prerequisites

- PHP 8.2+
- Composer 2.x
- Node.js 20.x
- NPM

### Installation

1. **Clone and setup the Laravel application:**
   ```bash
   cd contacts-app
   composer install
   ```

2. **Install frontend dependencies:**
   ```bash
   npm install
   ```

3. **Setup environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run database migrations and seeders:**
   ```bash
   php artisan migrate --seed
   ```

5. **Link storage for avatar uploads:**
   ```bash
   php artisan storage:link
   ```

6. **Start the development servers:**
   ```bash
   # Terminal 1: Laravel server
   php artisan serve
   
   # Terminal 2: Vite dev server
   npm run dev
   ```

The application will be available at `http://localhost:8000`

## Seeded Test Accounts

The database seeder creates the following test accounts:

- **admin@example.com** (password: `password`)
  - Admin role in Acme Corporation
  
- **member@example.com** (password: `password`)
  - Member role in Acme Corporation
  - Admin role in Tech Startup Inc
  
- **member2@example.com** (password: `password`)
  - Member role in Tech Startup Inc

## Testing

Run the test suite:

```bash
cd contacts-app
php artisan test
```

See [TESTS.md](TESTS.md) for detailed testing information and output.

## Key Implementation Details

### Organization Scoping

- **CurrentOrganizationServiceService**: Manages the current organization context from session
- **SetCurrentOrganizationService Middleware**: Ensures all requests have organization context
- **BelongsToOrganization Trait**: Automatically scopes all queries to current organization

### Duplicate Detection

When creating a contact with an existing email (case-insensitive), the system:

1. Returns HTTP 422 with exact payload: `{"code": "DUPLICATE_EMAIL", "existing_contact_id": "<uuid>"}`
2. Logs the attempt with organization, email, and user information
3. Does not create a duplicate contact

### Roles & Permissions

- **Admin**: Can manage organizations and contacts (CRUD, duplicate)
- **Member**: Can view contacts and manage their own notes

### API Endpoints

- `GET /healthz` - Health check endpoint
- Organization management routes
- Contact CRUD routes with duplicate detection
- Contact notes management
- Contact custom fields (meta) management

## Architecture Decisions

- **SQLite for Development**: Fast setup and testing
- **Minimalist UI**: Black and white design using Tailwind and shadcn/ui
- **Server-Side Logic Priority**: Focus on robust backend over UI polish
- **Strict Scoping**: Global scopes ensure no cross-organization data leaks

## Tradeoffs

- Simplified UI for faster development
- Basic empty states and minimal polish
- Focus on core functionality over advanced features
- SQLite default (MySQL support available)

I followed every instruction.

