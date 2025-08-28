<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMeta extends Model
{
    use HasFactory, HasUuids, BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'contact_id',
        'key',
        'value',
    ];

    protected $casts = [
        'id' => 'string',
        'organization_id' => 'string',
        'contact_id' => 'string',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}