<?php

namespace App\Services\Units;

use App\Models\Units;
use Illuminate\Pagination\LengthAwarePaginator;

interface UnitServices
{
    public function createUnits(array $attributes): Units;

    public function updateUnits(Units $units, array $attributes): Units;

    public function deleteUnits(Units $units): bool;

    public function getUnitsById(int $id): Units;

    public function paginateUnits(string $search = '', int $perPage = 10): LengthAwarePaginator;
}
