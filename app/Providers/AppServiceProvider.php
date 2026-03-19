<?php

namespace App\Providers;

use App\Services\Usermanagement\PermissionImpl;
use App\Services\Usermanagement\PermissionServices;
use App\Services\Usermanagement\RoleImpl;
use App\Services\Usermanagement\RoleServices;
use App\Services\Usermanagement\UserImpl;
use App\Services\Usermanagement\UserProfileImpl;
use App\Services\Usermanagement\UserProfileServices;
use App\Services\Usermanagement\UserServices;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserServices::class, UserImpl::class);
        $this->app->bind(RoleServices::class, RoleImpl::class);
        $this->app->bind(PermissionServices::class, PermissionImpl::class);
        $this->app->bind(UserProfileServices::class, UserProfileImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
