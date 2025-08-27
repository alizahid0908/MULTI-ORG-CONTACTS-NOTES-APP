# AI Usage Notes (AWS Kiro)

## Project Setup

### Laravel 12 Project Creation

**Prompt:** "Command to create Laravel 12 project."

- **Output:** Suggested `composer create-project laravel/laravel`
- **Decision:** Accepted as it matches spec requirement for Laravel 12
- **Reason:** Exact command needed for setup

### Laravel Breeze with Inertia React TypeScript

**Prompt:** "Steps to install Laravel Breeze with Inertia React TypeScript."

- **Output:** Suggested Breeze install commands with React TS
- **Decision:** Accepted as it aligns with spec. Rejected suggestion to include Vue option
- **Reason:** Spec mandates Inertia + React TS

### Spatie Laravel Permission Package

**Prompt:** "Install Spatie laravel-permission in Laravel 12."

- **Output:** Provided publish command for migrations
- **Decision:** Accepted publish command. Rejected additional role seeding suggestion to customize later per spec
- **Reason:** Role seeding will be customized in implementation

## UI Configuration

### Tailwind CSS Black & White Theme

**Prompt:** "Tailwind config for black-and-white only."

- **Output:** Suggested grayscale color palette
- **Decision:** Accepted grayscale palette. Rejected colorful palette (e.g., blue, red) to enforce spec's b&w requirement
- **Reason:** Spec requires black-and-white UI

### Shadcn/UI Setup

**Prompt:** "Setup shadcn/ui for React with Tailwind b&w theme."

- **Output:** Provided init and component add commands
- **Decision:** Accepted commands. Rejected default colorful theme, customized for grayscale
- **Reason:** Ensures compliance with b&w UI requirement

## Database & Storage Configuration

### SQLite Database Setup

**Prompt:** "Configure Laravel for SQLite and storage:link."

- **Output:** Provided .env config for SQLite and storage:link command
- **Decision:** Accepted configuration. Ensured no secrets committed per spec
- **Reason:** Matches spec's SQLite default and storage requirement

## API Endpoints

### Health Check Endpoint

**Prompt:** "Laravel route for /healthz returning { ok: true }."

- **Output:** Provided exact route definition
- **Decision:** Accepted as it matches spec beacon
- **Reason:** Required for rubric beacon compliance

## Database Migrations & Models

### Organizations Table Migration

**Prompt:** "Laravel migration for organizations table with UUID, name, slug, owner_user_id."

- **Output:** Created migration with UUID primary key, unique slug, foreign key constraints
- **Decision:** Accepted UUID approach and foreign key constraints for data integrity
- **Reason:** Matches DESIGN.md specifications exactly

### Organization-User Pivot Table

**Prompt:** "php artisan make:migration create_organization_user_table"

- **Output:** Created pivot table with role enum (Admin, Member), proper foreign keys
- **Decision:** Accepted enum approach for roles, added unique constraint for user-org pairs
- **Reason:** Enables many-to-many relationship with role-based permissions per spec

### Role Seeder for Spatie Permissions

**Prompt:** "php artisan make:seeder RoleSeeder"

- **Output:** Created seeder with Admin/Member roles and specific permissions
- **Decision:** Accepted permission structure: Admin (manage-organizations, manage-contacts), Member (view-contacts, manage-own-notes)
- **Reason:** Matches exact permission requirements from DESIGN.md

## Core Services & Architecture

### CurrentOrganization Service

**Prompt:** "Laravel service to manage current organization from session or user's first org."

- **Output:** Created singleton service with get/set methods, session management
- **Decision:** Accepted singleton pattern, session fallback logic, security validation
- **Reason:** Critical for organization scoping system per DESIGN.md

### SetCurrentOrganization Middleware

**Prompt:** "Laravel middleware to set current organization from service."

- **Output:** Created middleware with /healthz exception, 403 handling for no orgs
- **Decision:** Accepted global middleware approach, registered in bootstrap/app.php (Laravel 11 style)
- **Reason:** Ensures all routes (except /healthz) have organization context

### BelongsToOrganization Trait

**Prompt:** "Laravel trait for scoping models to current organization."

- **Output:** Created trait with global scope, auto-assignment on create, utility scopes
- **Decision:** Accepted global scope approach for automatic filtering, creating event for auto-assignment
- **Reason:** Prevents cross-org data access (critical security requirement)

## Models & Relationships

### Organization Model

**Prompt:** "Organization model"

- **Output:** Created model with UUID, relationships (owner, users, contacts), route key binding
- **Decision:** Accepted HasUuids trait, slug-based route binding, pivot relationships with roles
- **Reason:** Enables URL-friendly org switching and proper many-to-many relationships

## Controllers & Authorization

### OrganizationController

**Prompt:** "Laravel controller for organization create and switch."

- **Output:** Created controller with index, store, switch methods, auto-slug generation
- **Decision:** Accepted policy authorization, Spatie role assignment, dual JSON/redirect responses
- **Reason:** Handles both API and web requests, proper security with policies

### OrganizationPolicy

**Prompt:** "Laravel policy for organization create and switch."

- **Output:** Created policy with role-based and ownership-based authorization
- **Decision:** Accepted Admin role for creation, membership validation for switching, owner privileges
- **Reason:** Implements exact authorization rules from DESIGN.md

## Frontend Components

### Shadcn Select Component Installation

**Prompt:** "React TS component for shadcn Select to switch organizations."

- **Output:** Installed shadcn select component, created OrgSwitcher component
- **Decision:** Accepted black/white styling, Inertia router integration, proper TypeScript interfaces
- **Reason:** Matches UI requirements and provides seamless organization switching

### OrgSwitcher Component

- **Output:** Created component with POST to /organizations/switch, error handling, responsive design
- **Decision:** Accepted Inertia router approach, organization display with name/slug, empty state handling
- **Reason:** Provides user-friendly org switching with proper feedback

## Integration & Data Flow

### Inertia Middleware Updates

**Prompt:** "Update Inertia Middleware (to pass organizations and current org) and Routes in web.php"

- **Output:** Updated HandleInertiaRequests to share organization data globally, added organization routes
- **Decision:** Accepted global data sharing approach, flash message integration, proper route structure
- **Reason:** Makes organization data available on all pages, enables seamless component integration

### Route Structure

- **Output:** Added /organizations routes (index, store, switch) under auth middleware
- **Decision:** Accepted RESTful approach, proper middleware grouping, named routes
- **Reason:** Follows Laravel conventions and enables proper Inertia navigation
