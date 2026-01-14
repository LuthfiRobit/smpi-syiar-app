<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Repositories\Contracts\TeacherRepositoryInterface;

class TeacherSeeder extends Seeder
{
    protected $teacherRepository;

    public function __construct(TeacherRepositoryInterface $teacherRepository)
    {
        $this->teacherRepository = $teacherRepository;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = [
            [
                'nip' => '000001',
                'name' => 'Zaenal Abidin, S.Pd',
                'email' => 'zaenal@smpi-bisyril.sch.id',
                'gender' => 'L',
                'phone' => '081234567801',
                'address' => 'Sogaan, Kec. Pakuniran, Probolinggo',
            ],
            [
                'nip' => '000002',
                'name' => 'Angga Q. Zaman, S.Kom',
                'email' => 'angga@smpi-bisyril.sch.id',
                'gender' => 'L',
                'phone' => '081234567802',
                'address' => 'Sogaan, Kec. Pakuniran, Probolinggo',
            ],
            [
                'nip' => '000003',
                'name' => 'Husniyah, S.Pd',
                'email' => 'husniyah@smpi-bisyril.sch.id',
                'gender' => 'P',
                'phone' => '081234567803',
                'address' => 'Sogaan, Kec. Pakuniran, Probolinggo',
            ],
            [
                'nip' => '000004',
                'name' => 'Kholifaturrahmah, S.Pd',
                'email' => 'kholifaturrahmah@smpi-bisyril.sch.id',
                'gender' => 'P',
                'phone' => '081234567804',
                'address' => 'Sogaan, Kec. Pakuniran, Probolinggo',
            ],
            [
                'nip' => '000005',
                'name' => 'Luthfi Nuril Huda, M.Kom',
                'email' => 'luthfi@smpi-bisyril.sch.id',
                'gender' => 'L',
                'phone' => '081234567805',
                'address' => 'Sogaan, Kec. Pakuniran, Probolinggo',
            ],
            [
                'nip' => '000006',
                'name' => 'Zainul Anwar',
                'email' => 'zainul@smpi-bisyril.sch.id',
                'gender' => 'L',
                'phone' => '081234567806',
                'address' => 'Sogaan, Kec. Pakuniran, Probolinggo',
            ],
            [
                'nip' => '000007',
                'name' => 'Subaida, S.Pd',
                'email' => 'subaida@smpi-bisyril.sch.id',
                'gender' => 'P',
                'phone' => '081234567807',
                'address' => 'Sogaan, Kec. Pakuniran, Probolinggo',
            ],
            [
                'nip' => '000008',
                'name' => 'Yuni Malulida, S.Pd',
                'email' => 'yuni@smpi-bisyril.sch.id',
                'gender' => 'P',
                'phone' => '081234567808',
                'address' => 'Sogaan, Kec. Pakuniran, Probolinggo',
            ],
            [
                'nip' => '000009',
                'name' => 'Aris Setiawan, S.Pd',
                'email' => 'aris@smpi-bisyril.sch.id',
                'gender' => 'L',
                'phone' => '081234567809',
                'address' => 'Sogaan, Kec. Pakuniran, Probolinggo',
            ],
            [
                'nip' => '000010',
                'name' => 'Ust. Khofi',
                'email' => 'khofi@smpi-bisyril.sch.id',
                'gender' => 'L',
                'phone' => '081234567810',
                'address' => 'Sogaan, Kec. Pakuniran, Probolinggo',
            ],
            [
                'nip' => '000011',
                'name' => 'Moh. Bashari Awli, S.Pd',
                'email' => 'bashari@smpi-bisyril.sch.id',
                'gender' => 'L',
                'phone' => '081234567811',
                'address' => 'Sogaan, Kec. Pakuniran, Probolinggo',
            ],
        ];

        foreach ($teachers as $teacher) {
            $this->teacherRepository->create($teacher);
        }

        $this->command->info('✓ 11 teachers created successfully with User accounts');
    }
}
