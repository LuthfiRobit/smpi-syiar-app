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
                'nip' => '197501012000031001',
                'name' => 'Drs. Ahmad Hidayat, M.Pd',
                'email' => 'ahmad.hidayat@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '081234567801',
                'address' => 'Jl. Pendidikan No. 12, Jakarta Timur',
            ],
            [
                'nip' => '198003152005012002',
                'name' => 'Siti Nurhaliza, S.Pd',
                'email' => 'siti.nurhaliza@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '081234567802',
                'address' => 'Jl. Mawar No. 45, Jakarta Timur',
            ],
            [
                'nip' => '198507202010011003',
                'name' => 'Budi Santoso, S.Si, M.Pd',
                'email' => 'budi.santoso@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '081234567803',
                'address' => 'Jl. Melati No. 78, Jakarta Selatan',
            ],
            [
                'nip' => '198912102015012004',
                'name' => 'Dewi Lestari, S.Pd',
                'email' => 'dewi.lestari@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '081234567804',
                'address' => 'Jl. Anggrek No. 23, Jakarta Pusat',
            ],
            [
                'nip' => '199001052016011005',
                'name' => 'Rahmat Hidayat, S.Pd.I',
                'email' => 'rahmat.hidayat@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '081234567805',
                'address' => 'Jl. Dahlia No. 56, Jakarta Timur',
            ],
            [
                'nip' => '199205182017012006',
                'name' => 'Rina Andriani, S.Pd',
                'email' => 'rina.andriani@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '081234567806',
                'address' => 'Jl. Kenanga No. 89, Jakarta Barat',
            ],
            [
                'nip' => '198806252012011007',
                'name' => 'Fahmi Kurniawan, S.Pd',
                'email' => 'fahmi.kurniawan@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '081234567807',
                'address' => 'Jl. Cempaka No. 34, Jakarta Selatan',
            ],
            [
                'nip' => '199108302018012008',
                'name' => 'Sri Wahyuni, S.Pd',
                'email' => 'sri.wahyuni@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '081234567808',
                'address' => 'Jl. Teratai No. 67, Jakarta Utara',
            ],
            [
                'nip' => '198704122013011009',
                'name' => 'Yusuf Pratama, S.Kom',
                'email' => 'yusuf.pratama@smpi-syiar.sch.id',
                'gender' => 'L',
                'phone' => '081234567809',
                'address' => 'Jl. Flamboyan No. 90, Jakarta Timur',
            ],
            [
                'nip' => '199303202019012010',
                'name' => 'Fitri Handayani, S.Pd',
                'email' => 'fitri.handayani@smpi-syiar.sch.id',
                'gender' => 'P',
                'phone' => '081234567810',
                'address' => 'Jl. Bougenville No. 21, Jakarta Selatan',
            ],
        ];

        foreach ($teachers as $teacher) {
            $this->teacherRepository->create($teacher);
        }

        $this->command->info('✓ 10 teachers created successfully with User accounts');
    }
}
