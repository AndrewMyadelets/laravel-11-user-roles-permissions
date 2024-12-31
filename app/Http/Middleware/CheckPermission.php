<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $permissions = explode('|', $permission);

        if ($permission !== null && !$user->hasAnyPermissions($permissions)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
