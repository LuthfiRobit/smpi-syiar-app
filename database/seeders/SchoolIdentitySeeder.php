<?php

namespace Database\Seeders;

use App\Models\SchoolIdentity;
use Illuminate\Database\Seeder;

class SchoolIdentitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SchoolIdentity::create([
            'name' => 'SMP Islam Syiar',
            'address' => 'Jl. Pendidikan No. 123, Jakarta',
            'phone' => '021-12345678',
            'email' => 'info@smpi-syiar.sch.id',
            'website' => 'https://smpi-syiar.sch.id',
            'logo_path' => null,
            'headmaster_name' => 'Dr. Ahmad Hidayat, M.Pd',
            'headmaster_nip' => '196512311990031001',
        ]);
    }
}
