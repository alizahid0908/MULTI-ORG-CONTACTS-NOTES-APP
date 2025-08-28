<?php

namespace App\Policies;

use App\Models\ContactMeta;
use App\Models\User;

class ContactMetaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContactMeta $contactMeta): bool
    {
        return $user->hasRole('Admin') && 
               $user->organization_id === $contactMeta->organization_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContactMeta $contactMeta): bool
    {
        return $user->hasRole('Admin') && 
               $user->organization_id === $contactMeta->organization_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContactMeta $contactMeta): bool
    {
        return $user->hasRole('Admin') && 
               $user->organization_id === $contactMeta->organization_id;
    }
}