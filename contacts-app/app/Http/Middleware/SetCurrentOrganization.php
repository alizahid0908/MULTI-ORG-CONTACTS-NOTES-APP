<?php

namespace App\Http\Middleware;

use App\Services\CurrentOrganization;
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
        // Skip for /healthz route
        if ($request->is('healthz')) {
            return $next($request);
        }

        // Ensure user is authenticated
        if (!Auth::check()) {
            return $next($request);
        }

        $currentOrgService = app(CurrentOrganization::class);
        
        // Try to set organization from request parameter first
        if ($request->has('org_id')) {
            $currentOrgService->set($request->get('org_id'));
        }
        
        // Get current organization (will fall back to user's first org if none set)
        $currentOrg = $currentOrgService->get();
        
        // If user has no organizations, throw 403
        if (!$currentOrg) {
            abort(403, 'User does not belong to any organization.');
        }

        return $next($request);
    }
}
