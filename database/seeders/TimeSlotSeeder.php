<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TimeSlot;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        // Define day patterns
        $fullDays = ['Senin', 'Selasa', 'Rabu', 'Kamis']; // 07:00 - 16:00
        $shortDay = 'Jumat'; // 07:00 - 11:00
        $mediumDay = 'Sabtu'; // 07:00 - 13:00

        // 1. Senin - Kamis
        foreach ($fullDays as $day) {
            $this->createSlotsForDay($day, [
                // [Name, Start, End, IsBreak]
                ['Upacara/Wali Kelas', '07:00', '07:40', false],
                ['Jam Ke-1', '07:40', '08:20', false],
                ['Jam Ke-2', '08:20', '09:00', false],
                ['Jam Ke-3', '09:00', '09:40', false],
                ['Istirahat 1', '09:40', '10:00', true],
                ['Jam Ke-4', '10:00', '10:40', false],
                ['Jam Ke-5', '10:40', '11:20', false],
                ['Jam Ke-6', '11:20', '12:00', false],
                ['Ishoma', '12:00', '13:00', true],
                ['Jam Ke-7', '13:00', '13:40', false],
                ['Jam Ke-8', '13:40', '14:20', false],
            ]);
        }

        // 2. Jumat
        $this->createSlotsForDay($shortDay, [
            ['Pembiasaan', '07:00', '07:30', false],
            ['Jam Ke-1', '07:30', '08:10', false],
            ['Jam Ke-2', '08:10', '08:50', false],
            ['Istirahat', '08:50', '09:10', true],
            ['Jam Ke-3', '09:10', '09:50', false],
            ['Jam Ke-4', '09:50', '10:30', false],
        ]);

        // 3. Sabtu
        $this->createSlotsForDay($mediumDay, [
            ['Pembiasaan/Ekskul', '07:00', '08:00', false],
            ['Jam Ke-1', '08:00', '08:40', false],
            ['Jam Ke-2', '08:40', '09:20', false],
            ['Istirahat', '09:20', '09:40', true],
            ['Jam Ke-3', '09:40', '10:20', false],
            ['Jam Ke-4', '10:20', '11:00', false],
            ['Jam Ke-5', '11:00', '11:40', false],
        ]);
    }

    private function createSlotsForDay($day, $slots)
    {
        foreach ($slots as $slot) {
            TimeSlot::create([
                'day' => $day,
                'name' => $slot[0],
                'start_time' => $slot[1],
                'end_time' => $slot[2],
                'is_break' => $slot[3],
            ]);
        }
    }
}
