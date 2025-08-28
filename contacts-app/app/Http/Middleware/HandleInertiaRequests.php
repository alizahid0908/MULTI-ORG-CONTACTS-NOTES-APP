<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * Shares authentication data, organization context, and flash messages
     * with all Inertia.js pages for consistent state management.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $organizations = null;
        $CurrentOrganizationService = null;

        // Only load organization data for authenticated users
        if ($user) {
            $organizations = $user->organizations()->get();

            try {
                $CurrentOrganizationService = app(\App\Services\CurrentOrganizationService::class)->get();
            } catch (\Exception $e) {
                // Handle case where user has no organizations or service fails
                $CurrentOrganizationService = null;
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'organizations' => $organizations,
            'CurrentOrganizationService' => $CurrentOrganizationService,
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
            ],
            'errors' => $request->session()->get('errors') ? $request->session()->get('errors')->getBag('default')->getMessages() : (object) [],
        ];
    }
}
