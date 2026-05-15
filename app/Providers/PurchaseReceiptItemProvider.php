<?php

namespace App\Providers;

use App\Services\Purchase\PurchaseReceiptItemImpl;
use App\Services\Purchase\PurchaseReceiptItemServices;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class PurchaseReceiptItemProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings=[PurchaseReceiptItemServices::class => PurchaseReceiptItemImpl::class];
    public function provides(): array {
        return array_keys($this->bindings);
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
