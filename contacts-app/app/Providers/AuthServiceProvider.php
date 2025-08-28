<?php

namespace App\Providers;

use App\Models\Contact;
use App\Models\ContactMeta;
use App\Models\ContactNote;
use App\Models\Organization;
use App\Policies\ContactMetaPolicy;
use App\Policies\ContactNotePolicy;
use App\Policies\ContactPolicy;
use App\Policies\OrganizationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Contact::class => ContactPolicy::class,
        ContactNote::class => ContactNotePolicy::class,
        ContactMeta::class => ContactMetaPolicy::class,
        Organization::class => OrganizationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
