<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerGates();
        $this->setRedirectUri();
    }

    /**
     * Set the redirect URI.
     *
     * @return void
     */
    protected function setRedirectUri(): void
    {
        RedirectIfAuthenticated::redirectUsing(function (Request $request) {
            return $request->user()->redirectUri();
        });
    }

    /**
     * Register gates.
     *
     * @return void
     */
    protected function registerGates(): void
    {
        try {
            foreach (Permission::pluck('name') as $permission) {
                Gate::define($permission, function ($user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            }
        } catch (\Exception $e) {
            info('registerPermissions(): Database not found or not yet migrated. Ignoring user permissions while booting app.');
        }
    }
}
