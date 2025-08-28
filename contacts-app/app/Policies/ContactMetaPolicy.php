<?php

namespace App\Policies;

use App\Models\ContactMeta;
use App\Models\User;
use App\Services\CurrentOrganizationService;

class ContactMetaPolicy
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
    public function view(User $user, ContactMeta $contactMeta): bool
    {
        $currentOrg = $this->currentOrganizationService->get();
        
        if (!$currentOrg || $contactMeta->organization_id !== $currentOrg->id) {
            return false;
        }

        return $user->hasAnyRole(['Admin', 'Member'], $currentOrg);
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
    public function update(User $user, ContactMeta $contactMeta): bool
    {
        $currentOrg = $this->currentOrganizationService->get();
        
        if (!$currentOrg || $contactMeta->organization_id !== $currentOrg->id) {
            return false;
        }

        return $user->hasAnyRole(['Admin', 'Member'], $currentOrg);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContactMeta $contactMeta): bool
    {
        $currentOrg = $this->currentOrganizationService->get();
        
        if (!$currentOrg || $contactMeta->organization_id !== $currentOrg->id) {
            return false;
        }

        return $user->hasAnyRole(['Admin', 'Member'], $currentOrg);
    }
}
