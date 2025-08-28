<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactMetaController;
use App\Http\Controllers\ContactNoteController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/healthz', function () {
    return response()->json(['ok' => true]);
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Organization routes
    Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/organizations/create', [OrganizationController::class, 'create'])->name('organizations.create');
    Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');
    Route::post('/organizations/switch', [OrganizationController::class, 'switch'])->name('organizations.switch');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/create', [ContactController::class, 'create'])->name('contacts.create');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::get('/contacts/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    Route::get('/contacts/{contact}/duplicate', [ContactController::class, 'duplicate'])->name('contacts.duplicate');

    // Debug route
    Route::get('/debug/contacts', function () {
        $user = auth()->user();
        $currentOrg = app(\App\Services\CurrentOrganizationService::class)->get();
        $contacts = \App\Models\Contact::withoutGlobalScope('organization')->get();
        
        return response()->json([
            'user' => $user->name,
            'current_org' => $currentOrg ? $currentOrg->name : 'None',
            'current_org_id' => $currentOrg ? $currentOrg->id : 'None',
            'total_contacts' => $contacts->count(),
            'contacts_in_current_org' => $currentOrg ? \App\Models\Contact::where('organization_id', $currentOrg->id)->count() : 0,
            'sample_contacts' => $contacts->take(3)->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->full_name,
                'org_id' => $c->organization_id
            ])
        ]);
    });

    // Contact Notes routes
    Route::get('/contacts/{contact}/notes', [ContactNoteController::class, 'index'])->name('contact-notes.index');
    Route::post('/contacts/{contact}/notes', [ContactNoteController::class, 'store'])->name('contact-notes.store');
    Route::put('/contacts/{contact}/notes/{note}', [ContactNoteController::class, 'update'])->name('contact-notes.update');
    Route::delete('/contacts/{contact}/notes/{note}', [ContactNoteController::class, 'destroy'])->name('contact-notes.destroy');

    // Contact Meta routes
    Route::post('/contacts/{contact}/meta', [ContactMetaController::class, 'store'])->name('contact-meta.store');
    Route::delete('/contacts/{contact}/meta/{contactMeta}', [ContactMetaController::class, 'destroy'])->name('contact-meta.destroy');
});

require __DIR__.'/auth.php';
