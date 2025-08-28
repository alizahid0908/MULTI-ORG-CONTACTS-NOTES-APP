<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Services\CurrentOrganizationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
{
    use AuthorizesRequests;


    /**
     * List user's organizations.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $organizations = $user->organizations()->with('owner')->get();
        $currentOrg = app(CurrentOrganizationService::class)->get();

        return Inertia::render('Organizations/Index', [
            'organizations' => $organizations,
            'CurrentOrganizationService' => $currentOrg,
        ]);
    }

    /**
     * Show the form for creating a new organization.
     */
    public function create(): Response
    {
        return Inertia::render('Organizations/Create');
    }

    /**
     * Create a new organization and assign Admin role to creator.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Organization::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:organizations,slug',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);

            // Ensure slug is unique
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Organization::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug.'-'.$counter;
                $counter++;
            }
        }

        $user = Auth::user();

        $organization = Organization::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'owner_user_id' => $user->id,
        ]);

        // Attach user to organization with Admin role
        $organization->users()->attach($user->id, ['role' => 'Admin']);

        // Assign Admin role using Spatie permissions
        $user->assignRole('Admin');

        // Set as current organization
        app(CurrentOrganizationService::class)->set($organization->id);

        return redirect()->route('dashboard')
            ->with('success', 'Organization created successfully.');
    }

    /**
     * Switch current organization.
     */
    public function switch(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => 'required|string|exists:organizations,id',
        ]);

        $currentOrgService = app(CurrentOrganizationService::class);

        if ($currentOrgService->set($validated['organization_id'])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->back()->with('success', 'Organization switched successfully.');
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Access denied to organization.'], 403);
        }

        throw ValidationException::withMessages([
            'organization_id' => ['You do not have access to this organization.'],
        ]);
    }
}
