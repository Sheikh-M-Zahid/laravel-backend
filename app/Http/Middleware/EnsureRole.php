<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RBAC middleware. Usage in routes:
 *   Route::middleware(['auth', 'role:farmer'])->group(...)
 *   Route::middleware(['auth', 'role:admin,extension_officer'])->group(...)
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->status !== 'active') {
            abort(403, 'Your account is not active yet. Please wait for admin approval.');
        }

        if (! in_array($user->role, $roles, true)) {
            // Admin routes are also reachable by anyone the platform has
            // separately granted admin/super-admin permissions to, even if
            // their primary account role is farmer/officer/supplier -- this
            // is what lets a super admin (or an approved admin applicant)
            // jump into the admin dashboard without a second login.
            $isAdminRoute = in_array('admin', $roles, true);
            if ($isAdminRoute && ($user->is_admin || $user->is_super_admin)) {
                return $next($request);
            }

            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
