<?php

namespace App\Policies;

use App\Models\ContactNote;
use App\Models\User;
use App\Services\CurrentOrganizationService;

class ContactNotePolicy
{
    public function __construct(
        private CurrentOrganizationService $currentOrganizationService
    ) {}

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $currentOrg = $this->currentOrganizationService->get();

        return $currentOrg && $user->hasAnyRole(['Admin', 'Member'], $currentOrg);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContactNote $contactNote): bool
    {
        $currentOrg = $this->currentOrganizationService->get();

        if (! $currentOrg || $contactNote->organization_id !== $currentOrg->id) {
            return false;
        }

        // Admins can view all notes, users can view their own notes
        return $user->hasRole('Admin', $currentOrg) || $contactNote->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $currentOrg = $this->currentOrganizationService->get();

        return $currentOrg && $user->hasAnyRole(['Admin', 'Member'], $currentOrg);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContactNote $contactNote): bool
    {
        $currentOrg = $this->currentOrganizationService->get();

        if (! $currentOrg || $contactNote->organization_id !== $currentOrg->id) {
            return false;
        }

        // Admins can update all notes, users can update their own notes
        return $user->hasRole('Admin', $currentOrg) || $contactNote->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContactNote $contactNote): bool
    {
        $currentOrg = $this->currentOrganizationService->get();

        if (! $currentOrg || $contactNote->organization_id !== $currentOrg->id) {
            return false;
        }

        // Admins can delete all notes, users can delete their own notes
        return $user->hasRole('Admin', $currentOrg) || $contactNote->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ContactNote $contactNote): bool
    {
        return $this->update($user, $contactNote);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ContactNote $contactNote): bool
    {
        return $this->delete($user, $contactNote);
    }
}
