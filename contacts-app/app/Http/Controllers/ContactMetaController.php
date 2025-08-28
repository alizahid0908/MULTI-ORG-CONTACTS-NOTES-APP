<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMetaRequest;
use App\Models\Contact;
use App\Models\ContactMeta;
use App\Services\CurrentOrganizationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;

class ContactMetaController extends Controller
{
    use AuthorizesRequests;
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactMetaRequest $request, Contact $contact): RedirectResponse
    {
        $this->authorize('create', ContactMeta::class);

        $currentOrg = app(CurrentOrganizationService::class)->get();
        
        // Ensure contact belongs to current organization
        if (!$currentOrg || $contact->organization_id !== $currentOrg->id) {
            abort(403, 'Unauthorized access to contact.');
        }

        $contact->meta()->create([
            'organization_id' => $currentOrg->id,
            'key' => $request->validated('key'),
            'value' => $request->validated('value'),
        ]);

        return redirect()
            ->route('contacts.show', $contact)
            ->with('success', 'Custom field added successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact, ContactMeta $contactMeta): RedirectResponse
    {
        $this->authorize('delete', $contactMeta);

        $currentOrg = app(CurrentOrganizationService::class)->get();

        // Ensure contact and meta belong to current organization
        if (!$currentOrg || 
            $contact->organization_id !== $currentOrg->id ||
            $contactMeta->organization_id !== $currentOrg->id ||
            $contactMeta->contact_id !== $contact->id) {
            abort(403, 'Unauthorized access.');
        }

        $contactMeta->delete();

        return redirect()
            ->route('contacts.show', $contact)
            ->with('success', 'Custom field removed successfully.');
    }
}
