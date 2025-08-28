<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    /**
     * Determine whether the user can view any organizations.
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can view their organizations
        return true;
    }

    /**
     * Determine whether the user can view the organization.
     */
    public function view(User $user, Organization $organization): bool
    {
        // User can view if they belong to the organization
        return $user->organizations()->where('organizations.id', $organization->id)->exists();
    }

    /**
     * Determine whether the user can create organizations.
     * Any authenticated user can create their own organization.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create organizations
        return true;
    }

    /**
     * Determine whether the user can update the organization.
     */
    public function update(User $user, Organization $organization): bool
    {
        // Only organization owner or Admin can update
        return $organization->owner_user_id === $user->id ||
            ($user->hasRole('Admin') && $this->belongsToOrganization($user, $organization));
    }

    /**
     * Determine whether the user can delete the organization.
     */
    public function delete(User $user, Organization $organization): bool
    {
        // Only organization owner can delete
        return $organization->owner_user_id === $user->id;
    }

    /**
     * Determine whether the user can switch to this organization.
     * According to DESIGN.md: Admin can switch organizations.
     */
    public function switch(User $user, Organization $organization): bool
    {
        // User can switch if they belong to the organization
        return $this->belongsToOrganization($user, $organization);
    }

    /**
     * Determine whether the user can manage organization members.
     */
    public function manageMembers(User $user, Organization $organization): bool
    {
        // Only organization owner or Admin can manage members
        return $organization->owner_user_id === $user->id ||
            ($user->hasRole('Admin') && $this->belongsToOrganization($user, $organization));
    }

    /**
     * Helper method to check if user belongs to organization.
     */
    private function belongsToOrganization(User $user, Organization $organization): bool
    {
        return $user->organizations()->where('organizations.id', $organization->id)->exists();
    }

    /**
     * Unused methods set to false for security.
     */
    public function restore(User $user, Organization $organization): bool
    {
        return false;
    }

    public function forceDelete(User $user, Organization $organization): bool
    {
        return false;
    }
}
