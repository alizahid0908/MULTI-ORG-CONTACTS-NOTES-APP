# Design Plan for Multi-Organization Contacts and Notes App

## Overview

This document outlines the implementation of a Laravel 12 + Inertia/React TypeScript app for managing contacts and notes across multiple organizations, with strict org scoping, black-and-white UI, and exact adherence to the provided spec. The plan prioritizes core functionality (auth, orgs, contacts CRUD+duplicate, notes, custom fields) to meet rubric requirements in ~12 hours over 2 days.

## Models

### Organization:

Fields: id (uuid), name (string), slug (string, unique), owner_user_id (foreignId), timestamps.
Relations: hasMany Contacts, belongsToMany Users (via organization_user).
Notes: Slug for URL-friendly org switching.

### User:

Fields: Breeze defaults (id, name, email, password, email_verified_at, timestamps).
Relations: belongsToMany Organizations (via organization_user).

### organization_user (pivot):

Fields: organization_id, user_id, role (enum: Admin, Member), timestamps.
Notes: Stores roles for Spatie permissions.

### Contact:

Fields: id (uuid), organization_id (foreignId), first_name (string), last_name (string), email (string, nullable, unique per org, case-insensitive), phone (string, nullable), avatar_path (string, nullable), created_by (foreignId), updated_by (foreignId), timestamps.
Relations: belongsTo Organization, hasMany ContactNotes, hasMany ContactMeta.
Validation: Email unique per org (rule: unique:contacts,email,NULL,id,organization_id,$orgId), first_name/last_name required.
Trait: BelongsToOrganization for scoping.

### ContactNote:

Fields: id (uuid), contact_id (foreignId), user_id (foreignId), body (text), timestamps.
Relations: belongsTo Contact, belongsTo User.

### ContactMeta:

Fields: id (uuid), contact_id (foreignId), key (string), value (string), timestamps.
Relations: belongsTo Contact.
Notes: Max 5 per contact, enforced in controller.

## Routes

All routes except /healthz are under auth and set-current-organization middleware. Routes are grouped by prefix for clarity.

| Route                         | Method | Controller@Method             | Description                          |
| ----------------------------- | ------ | ----------------------------- | ------------------------------------ |
| /healthz                      | GET    | HealthController@index        | Returns { "ok": true }               |
| /organizations                | GET    | OrganizationController@index  | List user's orgs                     |
| /organizations                | POST   | OrganizationController@store  | Create org, assign Admin role        |
| /organizations/switch         | POST   | OrganizationController@switch | Set current org in session           |
| /contacts                     | GET    | ContactController@index       | List contacts (search by name/email) |
| /contacts                     | POST   | ContactController@store       | Create contact, check dedup          |
| /contacts/{id}                | GET    | ContactController@show        | Contact detail                       |
| /contacts/{id}                | PUT    | ContactController@update      | Update contact                       |
| /contacts/{id}                | DELETE | ContactController@destroy     | Delete contact                       |
| /contacts/{id}/duplicate      | POST   | ContactController@duplicate   | Duplicate contact (email=null)       |
| /contacts/{id}/notes          | GET    | NoteController@index          | List notes for contact               |
| /contacts/{id}/notes          | POST   | NoteController@store          | Add note                             |
| /contacts/{id}/notes/{noteId} | PUT    | NoteController@update         | Edit note                            |
| /contacts/{id}/notes/{noteId} | DELETE | NoteController@destroy        | Delete note                          |
| /contacts/{id}/meta           | POST   | MetaController@store          | Add key/value pair                   |
| /contacts/{id}/meta/{metaId}  | DELETE | MetaController@destroy        | Remove key/value pair                |

## Scoping

### CurrentOrganizationService Service:

Singleton, accessible via app(CurrentOrganizationService::class).
get(): Retrieves org_id from session (current_org_id) or user's first org (via pivot).
set($orgId): Stores org_id in session if user belongs to org.

### SetCurrentOrganizationService Middleware:

Runs on all routes (except /healthz).
Calls CurrentOrganizationService::set() with org_id from request or session; falls back to user's first org.
Throws 403 if user has no orgs.

### BelongsToOrganization Trait:

Used by Contact, ContactNote, ContactMeta.
Global scope: where('organization_id', app(CurrentOrganizationService::class)->get()->id).
On create: Sets organization_id to current org's ID.
Ensures no cross-org data access (critical to avoid hard fail).

## Roles (Spatie/laravel-permission)

### Setup:

Publish Spatie migrations, seed roles (Admin, Member).

### Permissions:

Admin: manage-organizations, manage-contacts (CRUD, duplicate).
Member: view-contacts, manage-own-notes (CRUD own notes).

### Policies:

OrganizationPolicy: Admin can create/switch.
ContactPolicy: Admin for CRUD/duplicate; Member for view.
NotePolicy: Users manage own notes (check user_id).
MetaPolicy: Admin for CRUD.

### Enforcement:

Use authorize() in controllers, Gates in UI (Inertia props).

## Deduplication Flow

### Server (ContactController@store):

Validate via ContactRequest (email unique per org).
Check for existing email: Contact::whereRaw('LOWER(email) = ?', [strtolower($request->email)])->where('organization_id', currentOrg()->id)->first().
If exists: Log duplicate_contact_blocked with { org_id, email, user_id }.
Return: response()->json(['code' => 'DUPLICATE_EMAIL', 'existing_contact_id' => $contact->id], 422).

### Client (ContactForm.tsx):

Inertia form's onError: Check if error.code == 'DUPLICATE_EMAIL'.
Redirect to /contacts/{existing_contact_id} using Inertia's router.
Flash message: "Duplicate detected. No new contact was created." (display via shadcn Alert).

### Notes:

Case-insensitive email check uses DB raw query to avoid collation issues.

## UI

### Design:

Minimalist, black-and-white, using Tailwind (grayscale only) and shadcn/ui (Button, Form, Table, Select, Input, Alert).

### Pages:

Auth: Breeze login/register with shadcn Form (b&w styling).
Org Switcher: shadcn Select dropdown in nav, lists user's orgs, POSTs to /organizations/switch.
Contacts List: shadcn Table (columns: name, email, phone, avatar), Input for search (name/email contains), empty state: "No contacts in this organization."
Contact Create/Edit: shadcn Form (fields: first_name, last_name, email, phone, avatar upload), validation errors shown inline.
Contact Detail: Displays contact info, shadcn Table for notes (add/edit/delete via Form), Form for meta (add/remove key/value, max 5), buttons for duplicate/delete.

### Components:

OrgSwitcher.tsx: Select with org list, submits on change.
ContactsTable.tsx: Table with search, avatar img (from storage/public).
ContactForm.tsx: Form with file input for avatar, handles 422 dedup redirect.
NotesSection.tsx: Table + Form for notes CRUD.
MetaSection.tsx: Form for key/value pairs, limit 5 (client/server validation).

### Error Handling:

Inertia flashes for validation, dedup message via Alert.

## Tests (Pest)

### Cross-Org Isolation:

Setup: Create OrgA, ContactA; create OrgB, switch user to OrgB.
Test: GET /contacts/{ContactA->id} → assert 403/404.
Ensures: No cross-org data exposure (hard fail avoidance).

### Deduplication:

Setup: Create contact with email in current org.
Test: POST /contacts with same email (case-insensitive) → assert 422, JSON { "code": "DUPLICATE_EMAIL", "existing_contact_id": <id> }, check log for duplicate_contact_blocked.
Ensures: Exact payload and behavior (hard fail avoidance).

## Tradeoffs

Use SQLite for speed (MySQL optional, skipped to save time).
Minimal UI: Functional shadcn components, basic empty states, no extra polish.
Prioritize server-side logic (e.g., dedup, scoping) over UI flair to ensure core functionality.
