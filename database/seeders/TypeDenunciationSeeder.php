<?php

namespace Database\Seeders;

use App\Models\TypeDenunciation;
use Illuminate\Database\Seeder;

class TypeDenunciationSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        TypeDenunciation::create([
            'name' => 'Fungsi Bangunan',
            'state' => 'active'
        ]);
    }
}
