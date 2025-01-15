<?php

namespace Database\Seeders;

use App\Models\FunctionBuilding;
use Illuminate\Database\Seeder;

class FunctionBuildingSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        FunctionBuilding::create([
            'name' => 'Jalanan',
            'state' => 'active'
        ]);
    }
}
