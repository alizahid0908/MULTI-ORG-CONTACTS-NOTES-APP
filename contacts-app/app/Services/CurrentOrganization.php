<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CurrentOrganization
{
    /**
     * Get the current organization from session or user's first org.
     */
    public function get(): ?Organization
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // Try to get org_id from session first
        $orgId = Session::get('current_org_id');

        if ($orgId) {
            $organization = $user->organizations()->where('organizations.id', $orgId)->first();
            if ($organization) {
                return $organization;
            }
        }

        // Fall back to user's first organization
        $firstOrg = $user->organizations()->first();

        if ($firstOrg) {
            // Store it in session for future requests
            Session::put('current_org_id', $firstOrg->id);
            return $firstOrg;
        }

        return null;
    }

    /**
     * Set the current organization in session if user belongs to it.
     */
    public function set(string $orgId): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Check if user belongs to this organization
        $organization = $user->organizations()->where('organizations.id', $orgId)->first();

        if ($organization) {
            Session::put('current_org_id', $orgId);
            return true;
        }

        return false;
    }

    /**
     * Clear the current organization from session.
     */
    public function clear(): void
    {
        Session::forget('current_org_id');
    }
}
