# Multi-Organization Contacts and Notes App

A Laravel 12 + Inertia/React TypeScript application for managing contacts and notes across multiple organizations with strict organizational scoping and black-and-white UI design.

## Features

- **Multi-Organization Support**: Users can belong to multiple organizations and switch between them
- **Contact Management**: Full CRUD operations for contacts with duplicate detection
- **Notes System**: Add, edit, and delete notes for each contact
- **Custom Fields**: Add up to 5 custom key-value pairs per contact
- **Role-Based Access**: Admin and Member roles with appropriate permissions
- **Avatar Support**: Upload and manage contact avatars
- **Search Functionality**: Search contacts by name or email
- **Duplicate Detection**: Prevents duplicate contacts based on email (case-insensitive)
- **Black & White UI**: Minimalist design using only black, white, and gray colors

## Tech Stack

- **Backend**: Laravel 12, PHP 8.3
- **Frontend**: React 19, TypeScript, Inertia.js
- **Styling**: Tailwind CSS, shadcn/ui components
- **Database**: SQLite (configurable to MySQL)
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission
- **Testing**: Pest PHP

## Installation

1. Clone the repository
2. Install PHP dependencies: `composer install`
3. Install Node dependencies: `npm install`
4. Copy environment file: `cp .env.example .env`
5. Generate application key: `php artisan key:generate`
6. Run migrations and seeders: `php artisan migrate --seed`
7. Create storage link: `php artisan storage:link`
8. Build assets: `npm run build`
9. Start the server: `php artisan serve`

## Demo Videos

### Codebase Walkthrough
https://www.loom.com/share/fdd9b20174594ff195788be430251702

### Core Functionality Demo
[Demo Video Link ]
Part 1: https://www.loom.com/share/5a8e292a3da34d0684f08cde135cc83e?sid=884e507a-cba9-4cc4-b9d9-7e97f9d7062e
Part 2: https://www.loom.com/share/8b8026d84d044fc5a682c5b97302d0d4?sid=475562ce-0a0d-41b8-9b19-dcb92a006734

## API Endpoints

### Health Check
- `GET /healthz` - Returns `{"ok": true}`

### Organizations
- `GET /organizations` - List user's organizations
- `POST /organizations` - Create new organization
- `POST /organizations/switch` - Switch current organization

### Contacts
- `GET /contacts` - List contacts with search
- `POST /contacts` - Create contact (with duplicate detection)
- `GET /contacts/{id}` - View contact details
- `PUT /contacts/{id}` - Update contact
- `DELETE /contacts/{id}` - Delete contact
- `GET /contacts/{id}/duplicate` - Duplicate contact form

### Notes
- `GET /contacts/{id}/notes` - List contact notes
- `POST /contacts/{id}/notes` - Add note
- `PUT /contacts/{id}/notes/{noteId}` - Update note
- `DELETE /contacts/{id}/notes/{noteId}` - Delete note

### Custom Fields
- `POST /contacts/{id}/meta` - Add custom field
- `DELETE /contacts/{id}/meta/{metaId}` - Remove custom field

## Testing

Run the test suite:
```bash
php artisan test
```

Key test coverage:
- Cross-organization data isolation
- Duplicate contact detection
- Role-based permissions
- Contact CRUD operations
- Notes management

## Architecture Highlights

### Organization Scoping
- `CurrentOrganizationService` manages current organization context
- `BelongsToOrganization` trait ensures data isolation
- Global scopes prevent cross-organization data access

### Duplicate Detection
- Case-insensitive email checking
- Returns structured error response for client handling
- Automatic redirect to existing contact

### Role-Based Access Control
- Admin: Full CRUD access to contacts and organizations
- Member: View contacts, manage own notes
- Enforced via Laravel policies and gates

## Security Features

- Strict organizational data scoping
- Role-based access control
- CSRF protection
- Input validation and sanitization
- File upload restrictions

## UI/UX Design

- Minimalist black and white design
- Responsive layout
- Intuitive navigation
- Clear error messaging
- Accessible form controls

I followed every instruction.