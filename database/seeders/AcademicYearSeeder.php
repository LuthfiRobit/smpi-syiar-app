<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AcademicYear::create([
            'name' => '2025/2026',
            'semester' => 'Genap',
            'is_active' => true,
        ]);

        AcademicYear::create([
            'name' => '2024/2025',
            'semester' => 'Ganjil',
            'is_active' => false,
        ]);
    }
}
