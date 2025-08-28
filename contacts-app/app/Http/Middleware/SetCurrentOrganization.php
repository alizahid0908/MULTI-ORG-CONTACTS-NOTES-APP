<?php

namespace App\Http\Middleware;

use App\Services\CurrentOrganizationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentOrganization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for certain routes that don't require organization context
        $skipRoutes = [
            'healthz',
            'profile',
            'profile/*',
            'verify-email',
            'verify-email/*',
            'confirm-password',
            'password',
            'logout',
            'login',
            'register',
            'forgot-password',
            'reset-password',
            'reset-password/*',
        ];

        foreach ($skipRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        // Ensure user is authenticated
        if (! Auth::check()) {
            return $next($request);
        }

        $currentOrgService = app(CurrentOrganizationService::class);

        // Try to set organization from request parameter first
        if ($request->has('org_id')) {
            $currentOrgService->set($request->get('org_id'));
        }

        // Get current organization (will fall back to user's first org if none set)
        $currentOrg = $currentOrgService->get();

        // If user has no organizations, throw 403
        if (! $currentOrg) {
            abort(403, 'User does not belong to any organization.');
        }

        return $next($request);
    }
}
