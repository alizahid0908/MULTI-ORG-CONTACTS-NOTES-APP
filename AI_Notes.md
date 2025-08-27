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
