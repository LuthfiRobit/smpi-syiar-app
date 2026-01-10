<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Repositories\Contracts\StudentRepositoryInterface;

class StudentSeeder extends Seeder
{
    protected $studentRepository;

    public function __construct(StudentRepositoryInterface $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            // Kelas VII
            [
                'nis' => '2024001',
                'nisn' => '0123456789',
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi.2024001@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111001',
                'address' => 'Jl. Kebon Jeruk No. 1, Jakarta Barat',
            ],
            [
                'nis' => '2024002',
                'nisn' => '0123456790',
                'name' => 'Siti Aisyah',
                'email' => 'siti.aisyah.2024002@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111002',
                'address' => 'Jl. Mangga Dua No. 2, Jakarta Utara',
            ],
            [
                'nis' => '2024003',
                'nisn' => '0123456791',
                'name' => 'Budi Setiawan',
                'email' => 'budi.setiawan.2024003@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111003',
                'address' => 'Jl. Sudirman No. 3, Jakarta Pusat',
            ],
            [
                'nis' => '2024004',
                'nisn' => '0123456792',
                'name' => 'Dewi Sartika',
                'email' => 'dewi.sartika.2024004@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111004',
                'address' => 'Jl. Thamrin No. 4, Jakarta Pusat',
            ],
            [
                'nis' => '2024005',
                'nisn' => '0123456793',
                'name' => 'Rizki Ramadhan',
                'email' => 'rizki.ramadhan.2024005@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111005',
                'address' => 'Jl. Gatot Subroto No. 5, Jakarta Selatan',
            ],
            [
                'nis' => '2024006',
                'nisn' => '0123456794',
                'name' => 'Nur Aini',
                'email' => 'nur.aini.2024006@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111006',
                'address' => 'Jl. Rasuna Said No. 6, Jakarta Selatan',
            ],
            [
                'nis' => '2024007',
                'nisn' => '0123456795',
                'name' => 'Fajar Nugroho',
                'email' => 'fajar.nugroho.2024007@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111007',
                'address' => 'Jl. Pancoran No. 7, Jakarta Selatan',
            ],
            [
                'nis' => '2024008',
                'nisn' => '0123456796',
                'name' => 'Laila Maharani',
                'email' => 'laila.maharani.2024008@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111008',
                'address' => 'Jl. Cikini No. 8, Jakarta Pusat',
            ],
            [
                'nis' => '2024009',
                'nisn' => '0123456797',
                'name' => 'Dimas Saputra',
                'email' => 'dimas.saputra.2024009@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111009',
                'address' => 'Jl. Menteng No. 9, Jakarta Pusat',
            ],
            [
                'nis' => '2024010',
                'nisn' => '0123456798',
                'name' => 'Ayu Lestari',
                'email' => 'ayu.lestari.2024010@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111010',
                'address' => 'Jl. Salemba No. 10, Jakarta Pusat',
            ],

            // Kelas VIII
            [
                'nis' => '2023001',
                'nisn' => '0123456799',
                'name' => 'Muhammad Hafiz',
                'email' => 'muhammad.hafiz.2023001@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111011',
                'address' => 'Jl. Tanah Abang No. 11, Jakarta Pusat',
            ],
            [
                'nis' => '2023002',
                'nisn' => '0123456800',
                'name' => 'Zahra Amelia',
                'email' => 'zahra.amelia.2023002@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111012',
                'address' => 'Jl. Kemang No. 12, Jakarta Selatan',
            ],
            [
                'nis' => '2023003',
                'nisn' => '0123456801',
                'name' => 'Arif Hidayat',
                'email' => 'arif.hidayat.2023003@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111013',
                'address' => 'Jl. Tebet No. 13, Jakarta Selatan',
            ],
            [
                'nis' => '2023004',
                'nisn' => '0123456802',
                'name' => 'Putri Ayu',
                'email' => 'putri.ayu.2023004@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111014',
                'address' => 'Jl. Mampang No. 14, Jakarta Selatan',
            ],
            [
                'nis' => '2023005',
                'nisn' => '0123456803',
                'name' => 'Hendra Wijaya',
                'email' => 'hendra.wijaya.2023005@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111015',
                'address' => 'Jl. Kuningan No. 15, Jakarta Selatan',
            ],
            [
                'nis' => '2023006',
                'nisn' => '0123456804',
                'name' => 'Indah Permata',
                'email' => 'indah.permata.2023006@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111016',
                'address' => 'Jl. Senopati No. 16, Jakarta Selatan',
            ],
            [
                'nis' => '2023007',
                'nisn' => '0123456805',
                'name' => 'Yoga Pratama',
                'email' => 'yoga.pratama.2023007@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111017',
                'address' => 'Jl. Blok M No. 17, Jakarta Selatan',
            ],
            [
                'nis' => '2023008',
                'nisn' => '0123456806',
                'name' => 'Rina Wulandari',
                'email' => 'rina.wulandari.2023008@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111018',
                'address' => 'Jl. Fatmawati No. 18, Jakarta Selatan',
            ],
            [
                'nis' => '2023009',
                'nisn' => '0123456807',
                'name' => 'Irfan Maulana',
                'email' => 'irfan.maulana.2023009@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111019',
                'address' => 'Jl. Cilandak No. 19, Jakarta Selatan',
            ],
            [
                'nis' => '2023010',
                'nisn' => '0123456808',
                'name' => 'Dina Mariana',
                'email' => 'dina.mariana.2023010@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111020',
                'address' => 'Jl. Lebak Bulus No. 20, Jakarta Selatan',
            ],

            // Kelas IX
            [
                'nis' => '2022001',
                'nisn' => '0123456809',
                'name' => 'Andi Firmansyah',
                'email' => 'andi.firmansyah.2022001@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111021',
                'address' => 'Jl. Pondok Indah No. 21, Jakarta Selatan',
            ],
            [
                'nis' => '2022002',
                'nisn' => '0123456810',
                'name' => 'Maya Sari',
                'email' => 'maya.sari.2022002@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111022',
                'address' => 'Jl. Kebayoran No. 22, Jakarta Selatan',
            ],
            [
                'nis' => '2022003',
                'nisn' => '0123456811',
                'name' => 'Rudi Hartono',
                'email' => 'rudi.hartono.2022003@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111023',
                'address' => 'Jl. Cipete No. 23, Jakarta Selatan',
            ],
            [
                'nis' => '2022004',
                'nisn' => '0123456812',
                'name' => 'Lia Amalia',
                'email' => 'lia.amalia.2022004@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111024',
                'address' => 'Jl. Pasar Minggu No. 24, Jakarta Selatan',
            ],
            [
                'nis' => '2022005',
                'nisn' => '0123456813',
                'name' => 'Wawan Setiawan',
                'email' => 'wawan.setiawan.2022005@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111025',
                'address' => 'Jl. Ragunan No. 25, Jakarta Selatan',
            ],
            [
                'nis' => '2022006',
                'nisn' => '0123456814',
                'name' => 'Nina Safitri',
                'email' => 'nina.safitri.2022006@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111026',
                'address' => 'Jl. Jagakarsa No. 26, Jakarta Selatan',
            ],
            [
                'nis' => '2022007',
                'nisn' => '0123456815',
                'name' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo.2022007@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111027',
                'address' => 'Jl. Lenteng Agung No. 27, Jakarta Selatan',
            ],
            [
                'nis' => '2022008',
                'nisn' => '0123456816',
                'name' => 'Sari Rahayu',
                'email' => 'sari.rahayu.2022008@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111028',
                'address' => 'Jl. Pasar Rebo No. 28, Jakarta Timur',
            ],
            [
                'nis' => '2022009',
                'nisn' => '0123456817',
                'name' => 'Agus Santoso',
                'email' => 'agus.santoso.2022009@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '082111111029',
                'address' => 'Jl. Cibubur No. 29, Jakarta Timur',
            ],
            [
                'nis' => '2022010',
                'nisn' => '0123456818',
                'name' => 'Tari Wulandari',
                'email' => 'tari.wulandari.2022010@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '082111111030',
                'address' => 'Jl. Cipayung No. 30, Jakarta Timur',
            ],
        ];

        foreach ($students as $student) {
            $this->studentRepository->create($student);
        }

        $this->command->info('✓ 30 students created successfully with User accounts (10 per grade: VII, VIII, IX)');
    }
}
