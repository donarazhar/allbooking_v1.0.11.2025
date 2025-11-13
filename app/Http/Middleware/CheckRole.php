<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        // Check if user has role relationship
        if (!$user->role) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Role pengguna tidak ditemukan. Silakan hubungi administrator.');
        }

        // Check if user is active
        if ($user->status_users !== 'active') {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda belum diaktifkan. Silakan hubungi administrator.');
        }

        // IMPORTANT: Map route parameter to role KODE
        $roleMapping = [
            'Admin' => ['SUPERADMIN', 'ADMIN'],
            'Pimpinan' => ['PIMPINAN'],
            'User' => ['USER'],
        ];

        // Get user role kode
        $userRoleKode = $user->role->kode;

        // Check if user's role kode is allowed
        $hasAccess = false;
        foreach ($roles as $role) {
            if (isset($roleMapping[$role]) && in_array($userRoleKode, $roleMapping[$role])) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            // Log unauthorized access attempt
            Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'user_name' => $user->nama,
                'user_role_kode' => $userRoleKode,
                'user_role_nama' => $user->role->nama,
                'attempted_roles' => $roles,
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
            ]);

            // Create custom 403 page or redirect
            return response()->view('errors.403', [
                'userRole' => $user->role->nama,
                'requiredRoles' => $roles,
            ], 403);
        }

        return $next($request);
    }
}
