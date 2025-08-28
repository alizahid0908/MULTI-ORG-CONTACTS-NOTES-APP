<?php

namespace App\Traits;

use App\Services\CurrentOrganizationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToOrganization
{
    /**
     * Boot the trait and add global scope for organization filtering.
     */
    protected static function bootBelongsToOrganization(): void
    {
        // Add global scope to filter by current organization
        static::addGlobalScope('organization', function (Builder $builder) {
            $currentOrg = app(CurrentOrganizationService::class)->get();

            if ($currentOrg) {
                $builder->where('organization_id', $currentOrg->id);
            }
        });

        // Automatically set organization_id when creating new models
        static::creating(function (Model $model) {
            if (! $model->organization_id) {
                $currentOrg = app(CurrentOrganizationService::class)->get();

                if ($currentOrg) {
                    $model->organization_id = $currentOrg->id;
                }
            }
        });
    }

    /**
     * Define the relationship to the organization.
     */
    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }

    /**
     * Scope query to a specific organization.
     */
    public function scopeForOrganization(Builder $query, string $organizationId): Builder
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Remove the global organization scope for cross-org queries (use carefully).
     */
    public function scopeWithoutOrganizationScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('organization');
    }
}
