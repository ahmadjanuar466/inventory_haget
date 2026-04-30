<?php

namespace App\Providers;

use App\Services\Purchase\PurchaseReceiptImpl;
use App\Services\Purchase\PurchaseReceiptServices;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Override;

class PurchaseReceiptProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings=[
        PurchaseReceiptServices::class=>PurchaseReceiptImpl::class
    ];
    public function provides() :array
    {
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
