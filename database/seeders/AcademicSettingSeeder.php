<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicSetting;

class AcademicSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create an academic year
        $academicYear = \App\Models\AcademicYear::where('is_active', true)->first();

        if (!$academicYear) {
            $academicYear = \App\Models\AcademicYear::first();
        }

        if ($academicYear) {
            // Default active days: Senin - Sabtu
            AcademicSetting::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'key' => 'active_days'
                ],
                [
                    'value' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                    'description' => 'Hari aktif kegiatan belajar mengajar'
                ]
            );
        }
    }
}
