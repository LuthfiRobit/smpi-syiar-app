<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TimeSlot;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Sabtu', 'Minggu'];

        foreach ($days as $day) {
            $this->createSlotsForDay($day, [
                [$day === 'Senin' ? 'Upacara Bendera' : 'Pembiasaan', '07:00', '07:30', false],
                ['Jam Ke-1', '07:30', '08:10', false],
                ['Jam Ke-2', '08:10', '08:50', false],
                ['Jam Ke-3', '08:50', '09:30', false],
                ['Jam Ke-4', '09:30', '10:10', false],
                ['Istirahat', '10:10', '10:30', true],
                ['Jam Ke-5', '10:30', '11:10', false],
                ['Jam Ke-6', '11:10', '11:50', false],
                ['Sholat Duhur Berjamaah', '11:50', '12:30', false],
            ]);
        }
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