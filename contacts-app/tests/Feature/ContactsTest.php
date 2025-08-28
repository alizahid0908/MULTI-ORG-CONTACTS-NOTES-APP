<?php

use App\Models\Contact;
use App\Models\Organization;
use App\Models\User;
use App\Services\CurrentOrganizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

test('cross-org isolation: org A cannot access org B contact', function () {
    // Create users for each organization
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    // Create two organizations
    $orgA = Organization::create([
        'name' => 'Organization A',
        'slug' => 'org-a',
        'owner_user_id' => $userA->id,
    ]);

    $orgB = Organization::create([
        'name' => 'Organization B',
        'slug' => 'org-b',
        'owner_user_id' => $userB->id,
    ]);

    // Assign users to organizations
    $orgA->users()->attach($userA->id, ['role' => 'Admin']);
    $orgB->users()->attach($userB->id, ['role' => 'Admin']);

    // Create a contact in Organization A
    $contactA = Contact::create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@orga.com',
        'organization_id' => $orgA->id,
        'created_by' => $userA->id,
        'updated_by' => $userA->id,
    ]);

    // Authenticate as user from Organization B
    $this->actingAs($userB);

    // Set current organization to B
    $currentOrgService = app(CurrentOrganizationService::class);
    $currentOrgService->set($orgB->id);

    // Try to access contact from Organization A - should get 404/403
    $response = $this->get("/contacts/{$contactA->id}");

    // Should be 404 because the contact doesn't exist in the scoped query
    $response->assertStatus(404);
});

test('duplicate email blocks creation and returns exact 422 payload', function () {
    // Create user and organization
    $user = User::factory()->create();

    $org = Organization::create([
        'name' => 'Test Organization',
        'slug' => 'test-org',
        'owner_user_id' => $user->id,
    ]);
    $org->users()->attach($user->id, ['role' => 'Admin']);

    // Create existing contact
    $existingContact = Contact::create([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@example.com',
        'organization_id' => $org->id,
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    // Authenticate user and set organization
    $this->actingAs($user);
    $currentOrgService = app(CurrentOrganizationService::class);
    $currentOrgService->set($org->id);

    // Mock the log expectation - allow any log calls
    Log::shouldReceive('info')->andReturn(null);
    Log::shouldReceive('error')->andReturn(null);

    // Try to create contact with same email (case-insensitive test)
    $response = $this->postJson('/contacts', [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'email' => 'JANE@EXAMPLE.COM', // Different case
        'phone' => '+1-555-0123',
    ]);

    // Assert exact 422 response with required payload
    $response->assertStatus(422);
    $response->assertJson([
        'code' => 'DUPLICATE_EMAIL',
        'existing_contact_id' => $existingContact->id,
    ]);
});

test('healthz endpoint returns ok', function () {
    $response = $this->get('/healthz');

    $response->assertStatus(200);
    $response->assertJson(['ok' => true]);
});
