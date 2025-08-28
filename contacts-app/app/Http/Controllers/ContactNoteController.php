<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactNoteRequest;
use App\Models\Contact;
use App\Models\ContactNote;
use App\Services\CurrentOrganizationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactNoteController extends Controller
{
    public function __construct(
        private CurrentOrganizationService $currentOrganizationService
    ) {}

    /**
     * Display a listing of notes for a contact.
     */
    public function index(Contact $contact)
    {
        $this->authorize('view', $contact);

        $notes = $contact->notes()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notes);
    }

    /**
     * Store a newly created note.
     */
    public function store(StoreContactNoteRequest $request, Contact $contact): RedirectResponse
    {
        $this->authorize('view', $contact);

        $note = new ContactNote([
            'body' => $request->validated()['body'],
            'user_id' => auth()->id(),
            'contact_id' => $contact->id,
            'organization_id' => $this->currentOrganizationService->get()->id,
        ]);

        $note->save();

        return back()->with('success', 'Note added successfully.');
    }

    /**
     * Update the specified note.
     */
    public function update(StoreContactNoteRequest $request, Contact $contact, ContactNote $note): RedirectResponse
    {
        $this->authorize('view', $contact);
        $this->authorize('update', $note);

        $note->update([
            'body' => $request->validated()['body'],
        ]);

        return back()->with('success', 'Note updated successfully.');
    }

    /**
     * Remove the specified note.
     */
    public function destroy(Contact $contact, ContactNote $note): RedirectResponse
    {
        $this->authorize('view', $contact);
        $this->authorize('delete', $note);

        $note->delete();

        return back()->with('success', 'Note deleted successfully.');
    }
}
