<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use App\Services\CurrentOrganizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** 
     * List contacts with search functionality.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Contact::class);

        $search = $request->get('search');

        $contacts = Contact::query()
            ->when($search, function ($query, $search) {
                $query->search($search);
            })
            ->with(['creator', 'updater'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts,
            'search' => $search,
            'can' => [
                'create' => auth()->user()->can('create', Contact::class),
            ],
        ]);
    }

    /**
     * Show the form for creating a new contact.
     */
    public function create(): Response
    {
        $this->authorize('create', Contact::class);

        return Inertia::render('Contacts/Create');
    }

    /**
     * Show contact detail.
     */
    public function show(Contact $contact): Response
    {
        $this->authorize('view', $contact);

        $contact->load(['creator', 'updater', 'notes.user', 'meta']);

        return Inertia::render('Contacts/Show', [
            'contact' => $contact,
            'can' => [
                'update' => auth()->user()->can('update', $contact),
                'delete' => auth()->user()->can('delete', $contact),
                'duplicate' => auth()->user()->can('duplicate', $contact),
                'createNotes' => auth()->user()->can('create', \App\Models\ContactNote::class),
                'manageMeta' => auth()->user()->can('create', \App\Models\ContactMeta::class),
            ],
        ]);
    }

    /**
     * Show the form for editing the contact.
     */
    public function edit(Contact $contact): Response
    {
        $this->authorize('update', $contact);

        return Inertia::render('Contacts/Edit', [
            'contact' => $contact,
        ]);
    }

    /**
     * Create contact with duplicate detection.
     */
    public function store(StoreContactRequest $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Contact::class);

        $currentOrg = app(CurrentOrganizationService::class)->get();

        // Check for existing email (case-insensitive) for deduplication
        if ($request->email) {
            $existingContact = Contact::whereRaw('LOWER(email) = ?', [strtolower($request->email)])
                ->where('organization_id', $currentOrg->id)
                ->first();

            if ($existingContact) {
                // Log duplicate attempt as specified in DESIGN.md
                Log::info('duplicate_contact_blocked', [
                    'org_id' => $currentOrg->id,
                    'email' => $request->email,
                    'user_id' => auth()->id(),
                ]);

                // Return exact response format from DESIGN.md
                if ($request->expectsJson()) {
                    return response()->json([
                        'code' => 'DUPLICATE_EMAIL',
                        'existing_contact_id' => $existingContact->id,
                    ], 422);
                }

                return redirect()->route('contacts.show', $existingContact)
                    ->with('error', 'Duplicate detected. No new contact was created.');
            }
        }

        $validatedData = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validatedData['avatar_path'] = $avatarPath;
        }

        $contact = Contact::create($validatedData);

        if ($request->expectsJson()) {
            return response()->json($contact->load(['creator', 'updater']), 201);
        }

        return redirect()->route('contacts.show', $contact)
            ->with('success', 'Contact created successfully.');
    }

    /**
     * Update contact.
     */
    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse|RedirectResponse
    {
        $validatedData = $request->validatedForUpdate();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($contact->avatar_path) {
                Storage::disk('public')->delete($contact->avatar_path);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validatedData['avatar_path'] = $avatarPath;
        }

        // Handle avatar removal
        if ($request->boolean('remove_avatar') && $contact->avatar_path) {
            Storage::disk('public')->delete($contact->avatar_path);
            $validatedData['avatar_path'] = null;
        }

        $contact->update($validatedData);

        if ($request->expectsJson()) {
            return response()->json($contact->fresh(['creator', 'updater']));
        }

        return redirect()->route('contacts.show', $contact)
            ->with('success', 'Contact updated successfully.');
    }

    /**
     * Delete contact.
     */
    public function destroy(Contact $contact): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $contact);

        // Delete avatar file if exists
        if ($contact->avatar_path) {
            Storage::disk('public')->delete($contact->avatar_path);
        }

        $contact->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Contact deleted successfully.']);
        }

        return redirect()->route('contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }

    /**
     * Duplicate contact (email set to null as specified in DESIGN.md).
     */
    public function duplicate(Contact $contact): JsonResponse|RedirectResponse
    {
        $this->authorize('duplicate', $contact);

        $duplicatedContact = $contact->replicate();

        // Set email to null as specified in DESIGN.md
        $duplicatedContact->email = null;
        $duplicatedContact->created_by = auth()->id();
        $duplicatedContact->updated_by = auth()->id();

        // Don't duplicate avatar, user can upload new one
        $duplicatedContact->avatar_path = null;

        $duplicatedContact->save();

        if (request()->expectsJson()) {
            return response()->json($duplicatedContact->load(['creator', 'updater']), 201);
        }

        return redirect()->route('contacts.show', $duplicatedContact)
            ->with('success', 'Contact duplicated successfully.');
    }
}
