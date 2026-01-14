<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            ['code' => 'TLM', 'name' => 'Ta\'lim'],
            ['code' => 'BIND', 'name' => 'Bahasa Indonesia'],
            ['code' => 'BARB', 'name' => 'Bahasa Arab'],
            ['code' => 'BING', 'name' => 'Bahasa Inggris'],
            ['code' => 'MTK', 'name' => 'Matematika'],
            ['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam'],
            ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial'],
            ['code' => 'PAI', 'name' => 'Pendidikan Agama Islam'],
            ['code' => 'PJOK', 'name' => 'Pendidikan Jasmani Olahraga dan Kesehatan'],
            ['code' => 'ASW', 'name' => 'Aswaja'],
            ['code' => 'PRAK', 'name' => 'Prakarya'],
            ['code' => 'SBY', 'name' => 'Seni Budaya'],
            ['code' => 'INF', 'name' => 'Informatika'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
