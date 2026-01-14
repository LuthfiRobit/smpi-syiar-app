<?php

namespace App\Repositories\Eloquents;

use App\Models\TeachingMaterial;
use App\Models\TeachingMaterialType;
use App\Repositories\Contracts\TeachingMaterialRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TeachingMaterialRepository implements TeachingMaterialRepositoryInterface
{
    protected $model;
    protected $typeModel;

    public function __construct(TeachingMaterial $model, TeachingMaterialType $typeModel)
    {
        $this->model = $model;
        $this->typeModel = $typeModel;
    }

    public function getAllTypes()
    {
        return $this->typeModel->all();
    }

    public function getByTeacherAndYear($teacherId, $yearId)
    {
        return $this->model->with(['type', 'subject'])
            ->where('teacher_id', $teacherId)
            ->where('academic_year_id', $yearId)
            ->get();
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function update($id, array $data)
    {
        $material = $this->find($id);
        $material->update($data);
        return $material;
    }

    public function delete($id)
    {
        $material = $this->find($id);
        return $material->delete();
    }

    public function getSummaryByYear($yearId)
    {
        // This is complex. We need to list all teachers and their progress.
        // For now, let's return just the materials with details to be processed in controller/view or enhanced later.
        // Better approach: Get all teachers, load their materials for this year.

        $teachers = \App\Models\Teacher::with([
            'teachingMaterials' => function ($q) use ($yearId) {
                $q->where('academic_year_id', $yearId);
            }
        ])->whereHas('user', function ($q) {
            $q->where('is_active', true);
        })->get();

        return $teachers;
    }
}
