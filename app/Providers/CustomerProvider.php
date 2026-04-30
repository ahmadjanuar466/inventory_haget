<?php

namespace App\Providers;

use App\Services\Customer\CustomerImpl;
use App\Services\Customer\CustomerServices;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class CustomerProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings = [
        CustomerServices::class => CustomerImpl::class,
    ];
    public function provides(): array
    {
        return [
            CustomerServices::class,
        ];
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
