<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PicketSchedule;
use App\Models\Teacher;
use App\Models\AcademicYear;

class PicketScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeYear = \App\Models\AcademicYear::where('is_active', '=', true)->first();
        if (!$activeYear)
            return;

        $teachers = Teacher::all();
        if ($teachers->isEmpty())
            return;

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Assign 2 teachers per day
        foreach ($days as $index => $day) {
            $teacher1 = $teachers->get(($index * 2) % $teachers->count());
            $teacher2 = $teachers->get(($index * 2 + 1) % $teachers->count());

            PicketSchedule::create([
                'academic_year_id' => $activeYear->id,
                'teacher_id' => $teacher1->id,
                'day' => $day,
            ]);

            PicketSchedule::create([
                'academic_year_id' => $activeYear->id,
                'teacher_id' => $teacher2->id,
                'day' => $day,
            ]);
        }

        $this->command->info('✓ Picket schedules created successfully');
    }
}
