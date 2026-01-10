<?php

namespace App\Repositories\Eloquents;

use App\Models\AcademicYear;
use App\Repositories\Contracts\AcademicYearRepositoryInterface;

class AcademicYearRepository implements AcademicYearRepositoryInterface
{
    protected $model;

    public function __construct(AcademicYear $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->orderBy('name', 'desc')->get();
    }

    public function findById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function getActive()
    {
        return $this->model->where('is_active', true)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $academicYear = $this->findById($id);
        $academicYear->update($data);
        return $academicYear;
    }

    public function delete(int $id)
    {
        $academicYear = $this->findById($id);
        return $academicYear->delete();
    }

    public function setActive(int $id)
    {
        // Nonaktifkan semua tahun ajaran
        $this->model->where('is_active', true)->update(['is_active' => false]);

        // Aktifkan tahun ajaran yang dipilih
        $academicYear = $this->findById($id);
        $academicYear->update(['is_active' => true]);

        return $academicYear;
    }
}
