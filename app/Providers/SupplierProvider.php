<?php

namespace App\Providers;

use App\Services\Supplier\SupplierImpl;
use App\Services\Supplier\SupplierServices;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SupplierProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings = [SupplierServices::class => SupplierImpl::class];
    public function provides(): array
    {
        return [SupplierServices::class];
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
