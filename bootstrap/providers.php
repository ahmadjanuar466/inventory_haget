<?php

use App\Providers\AppServiceProvider;
use App\Providers\BranchProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\WarehouseProvider;
use Spatie\Permission\PermissionServiceProvider;

return [
    AppServiceProvider::class,
    BranchProvider::class,
    FortifyServiceProvider::class,
    WarehouseProvider::class,
    PermissionServiceProvider::class,
];
