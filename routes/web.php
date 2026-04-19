<?php

use App\Livewire\Branch\BranchesIndex;
use App\Livewire\Category\CategoriesIndex;
use App\Livewire\Product\ProductIndex;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Unit\UnitsIndex;
use App\Livewire\Usermanagement\PermissionsIndex;
use App\Livewire\Usermanagement\RolesIndex;
use App\Livewire\Usermanagement\UsersIndex;
use App\Livewire\Warehouse\WarehousesIndex;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('master/branches', BranchesIndex::class)->name('master.branches');
    Route::get('master/warehouses', WarehousesIndex::class)->name('master.warehouses');
    Route::get('master/products', ProductIndex::class)->name('master.products');
    Route::get('master/product-categories', CategoriesIndex::class)->name('master.product-categories');
    Route::get('master/product-units', UnitsIndex::class)->name('master.product-units');

    Route::get('user-management/users', UsersIndex::class)->name('user-management.users');
    Route::get('user-management/roles', RolesIndex::class)->name('user-management.roles');
    Route::get('user-management/permissions', PermissionsIndex::class)->name('user-management.permissions');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
