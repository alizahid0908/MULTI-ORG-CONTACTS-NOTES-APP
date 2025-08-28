<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMetaRequest;
use App\Models\Contact;
use App\Models\ContactMeta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactMetaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactMetaRequest $request, Contact $contact): RedirectResponse
    {
        $this->authorize('create', ContactMeta::class);

        // Ensure contact belongs to current organization
        if ($contact->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized access to contact.');
        }

        $contact->meta()->create([
            'organization_id' => auth()->user()->organization_id,
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

        // Ensure contact and meta belong to current organization
        if ($contact->organization_id !== auth()->user()->organization_id ||
            $contactMeta->organization_id !== auth()->user()->organization_id ||
            $contactMeta->contact_id !== $contact->id) {
            abort(403, 'Unauthorized access.');
        }

        $contactMeta->delete();

        return redirect()
            ->route('contacts.show', $contact)
            ->with('success', 'Custom field removed successfully.');
    }
}