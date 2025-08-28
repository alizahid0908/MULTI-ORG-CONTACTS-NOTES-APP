<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\ContactNote;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the RoleSeeder first to create roles and permissions
        $this->call(RoleSeeder::class);

        // Create test users
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $memberUser = User::factory()->create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'password' => Hash::make('password'),
        ]);

        $secondMemberUser = User::factory()->create([
            'name' => 'Second Member',
            'email' => 'member2@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create test organizations
        $acmeOrg = Organization::create([
            'name' => 'Acme Corporation',
            'slug' => 'acme-corp',
            'owner_user_id' => $adminUser->id,
        ]);

        $techOrg = Organization::create([
            'name' => 'Tech Startup Inc',
            'slug' => 'tech-startup',
            'owner_user_id' => $memberUser->id,
        ]);

        // Assign users to organizations with roles
        $acmeOrg->users()->attach($adminUser->id, ['role' => 'Admin']);
        $acmeOrg->users()->attach($memberUser->id, ['role' => 'Member']);

        $techOrg->users()->attach($memberUser->id, ['role' => 'Admin']);
        $techOrg->users()->attach($secondMemberUser->id, ['role' => 'Member']);

        // Assign Spatie roles to users
        $adminUser->assignRole('Admin');
        $memberUser->assignRole('Member');
        $secondMemberUser->assignRole('Member');

        // Seed contacts for Acme Corporation (manually set organization_id)
        $johnDoe = Contact::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1-555-0123',
            'organization_id' => $acmeOrg->id,
            'created_by' => $adminUser->id,
            'updated_by' => $adminUser->id,
        ]);

        $janeSmith = Contact::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'phone' => '+1-555-0124',
            'organization_id' => $acmeOrg->id,
            'created_by' => $memberUser->id,
            'updated_by' => $memberUser->id,
        ]);

        $bobJohnson = Contact::create([
            'first_name' => 'Bob',
            'last_name' => 'Johnson',
            'email' => 'bob.johnson@example.com',
            'phone' => '+1-555-0125',
            'organization_id' => $acmeOrg->id,
            'created_by' => $adminUser->id,
            'updated_by' => $adminUser->id,
        ]);

        // Seed contacts for Tech Startup
        $aliceWilson = Contact::create([
            'first_name' => 'Alice',
            'last_name' => 'Wilson',
            'email' => 'alice.wilson@techstartup.com',
            'phone' => '+1-555-0200',
            'organization_id' => $techOrg->id,
            'created_by' => $memberUser->id,
            'updated_by' => $memberUser->id,
        ]);

        $charlieGreen = Contact::create([
            'first_name' => 'Charlie',
            'last_name' => 'Green',
            'email' => 'charlie.green@techstartup.com',
            'phone' => '+1-555-0201',
            'organization_id' => $techOrg->id,
            'created_by' => $secondMemberUser->id,
            'updated_by' => $secondMemberUser->id,
        ]);

        // Seed contact notes
        ContactNote::create([
            'contact_id' => $johnDoe->id,
            'user_id' => $adminUser->id,
            'organization_id' => $acmeOrg->id,
            'body' => 'Initial contact made. Very interested in our services.',
        ]);

        ContactNote::create([
            'contact_id' => $johnDoe->id,
            'user_id' => $memberUser->id,
            'organization_id' => $acmeOrg->id,
            'body' => 'Follow-up call scheduled for next week.',
        ]);

        ContactNote::create([
            'contact_id' => $janeSmith->id,
            'user_id' => $memberUser->id,
            'organization_id' => $acmeOrg->id,
            'body' => 'Sent proposal via email. Awaiting response.',
        ]);

        ContactNote::create([
            'contact_id' => $aliceWilson->id,
            'user_id' => $memberUser->id,
            'organization_id' => $techOrg->id,
            'body' => 'Technical discussion about integration requirements.',
        ]);

        ContactNote::create([
            'contact_id' => $charlieGreen->id,
            'user_id' => $secondMemberUser->id,
            'organization_id' => $techOrg->id,
            'body' => 'Meeting scheduled to discuss project timeline.',
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Test users created:');
        $this->command->info('- admin@example.com (password: password) - Admin at Acme Corp');
        $this->command->info('- member@example.com (password: password) - Member at Acme Corp, Admin at Tech Startup');
        $this->command->info('- member2@example.com (password: password) - Member at Tech Startup');
        $this->command->info('Organizations created: Acme Corporation, Tech Startup Inc');
        $this->command->info('Contacts and notes seeded for both organizations');
    }
}
