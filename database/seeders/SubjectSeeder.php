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
            ['code' => 'MTK', 'name' => 'Matematika'],
            ['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam'],
            ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial'],
            ['code' => 'BIND', 'name' => 'Bahasa Indonesia'],
            ['code' => 'BING', 'name' => 'Bahasa Inggris'],
            ['code' => 'PJOK', 'name' => 'Pendidikan Jasmani Olahraga dan Kesehatan'],
            ['code' => 'PAI', 'name' => 'Pendidikan Agama Islam'],
            ['code' => 'PKN', 'name' => 'Pendidikan Kewarganegaraan'],
            ['code' => 'SBK', 'name' => 'Seni Budaya dan Keterampilan'],
            ['code' => 'PRAKARYA', 'name' => 'Prakarya'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
