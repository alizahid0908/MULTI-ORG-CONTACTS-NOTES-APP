<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;
use App\Services\CurrentOrganization;
use Illuminate\Auth\Access\Response;

class ContactPolicy
{
    /**
     * Determine whether the user can view any contacts.
     * According to DESIGN.md: Admin for CRUD, Member for view.
     */
    public function viewAny(User $user): bool
    {
        // Both Admin and Member can view contacts
        return $user->hasRole('Admin') ||
            $user->hasRole('Member') ||
            $user->can('view-contacts') ||
            $user->can('manage-contacts');
    }

    /**
     * Determine whether the user can view the contact.
     * Must belong to same organization and have view permissions.
     */
    public function view(User $user, Contact $contact): bool
    {
        // Check if contact belongs to user's current organization
        if (!$this->belongsToCurrentOrganization($contact)) {
            return false;
        }

        // Both Admin and Member can view contacts
        return $user->hasRole('Admin') ||
            $user->hasRole('Member') ||
            $user->can('view-contacts') ||
            $user->can('manage-contacts');
    }

    /**
     * Determine whether the user can create contacts.
     * According to DESIGN.md: Admin can manage contacts (CRUD).
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin') || $user->can('manage-contacts');
    }

    /**
     * Determine whether the user can update the contact.
     * According to DESIGN.md: Admin for CRUD.
     */
    public function update(User $user, Contact $contact): bool
    {
        // Check if contact belongs to user's current organization
        if (!$this->belongsToCurrentOrganization($contact)) {
            return false;
        }

        return $user->hasRole('Admin') || $user->can('manage-contacts');
    }

    /**
     * Determine whether the user can delete the contact.
     * According to DESIGN.md: Admin for CRUD.
     */
    public function delete(User $user, Contact $contact): bool
    {
        // Check if contact belongs to user's current organization
        if (!$this->belongsToCurrentOrganization($contact)) {
            return false;
        }

        return $user->hasRole('Admin') || $user->can('manage-contacts');
    }

    /**
     * Determine whether the user can duplicate the contact.
     * According to DESIGN.md: Admin for duplicate.
     */
    public function duplicate(User $user, Contact $contact): bool
    {
        // Check if contact belongs to user's current organization
        if (!$this->belongsToCurrentOrganization($contact)) {
            return false;
        }

        return $user->hasRole('Admin') || $user->can('manage-contacts');
    }

    /**
     * Determine whether the user can export contacts.
     */
    public function export(User $user): bool
    {
        // Both Admin and Member can export their organization's contacts
        return $user->hasRole('Admin') ||
            $user->hasRole('Member') ||
            $user->can('view-contacts') ||
            $user->can('manage-contacts');
    }

    /**
     * Determine whether the user can import contacts.
     */
    public function import(User $user): bool
    {
        // Only Admin can import contacts
        return $user->hasRole('Admin') || $user->can('manage-contacts');
    }

    /**
     * Helper method to check if contact belongs to current organization.
     * This provides an extra layer of security beyond the global scope.
     */
    private function belongsToCurrentOrganization(Contact $contact): bool
    {
        $currentOrg = app(CurrentOrganization::class)->get();

        if (!$currentOrg) {
            return false;
        }

        return $contact->organization_id === $currentOrg->id;
    }

    /**
     * Unused methods set to false for security.
     */
    public function restore(User $user, Contact $contact): bool
    {
        return false;
    }

    public function forceDelete(User $user, Contact $contact): bool
    {
        return false;
    }
}
