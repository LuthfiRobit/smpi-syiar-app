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
            'name' => 'SMP Islam Bisyril Arifin',
            'address' => 'Jl. PP. Bisyril Arifin, Sogaan, Pakuniran',
            'phone' => '021-12345678',
            'email' => 'info@smpi-syiar.sch.id',
            'website' => 'https://smpi-syiar.sch.id',
            'logo_path' => null,
            'headmaster_name' => 'Zaenal Abidin, S.Pd',
            'headmaster_nip' => '-',
        ]);
    }
}
