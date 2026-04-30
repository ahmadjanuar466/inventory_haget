<?php

namespace App\Providers;

use App\Services\Customer\CustomerTypeImpl;
use App\Services\Customer\CustomerTypeServices;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class CustomerTypeProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings = [CustomerTypeServices::class => CustomerTypeImpl::class];
    public function provides(): array
    {
        return [CustomerTypeServices::class];
    }
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
        //
    }
}
