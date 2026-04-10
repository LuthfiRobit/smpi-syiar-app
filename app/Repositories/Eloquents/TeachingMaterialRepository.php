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
            ->orderBy('grade_level')
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

    /**
     * Per-teacher summary for admin monitoring table.
     * Returns teachers with counts of approved/pending/rejected modules.
     */
    public function getSummaryByYear($yearId)
    {
        $teachers = \App\Models\Teacher::with([
            'teachingMaterials' => function ($q) use ($yearId) {
                $q->where('academic_year_id', $yearId);
            }
        ])->whereHas('user', function ($q) {
            $q->where('is_active', true);
        })->get();

        return $teachers;
    }

    /**
     * Flat paginated module list for the Admin "Per Modul" tab.
     */
    public function getModulesForAdmin($yearId, $teacherId = null, $gradeLevel = null, $subjectId = null, $status = null)
    {
        $query = $this->model
            ->with(['teacher', 'type', 'subject'])
            ->where('academic_year_id', $yearId);

        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }

        if ($gradeLevel) {
            $query->where('grade_level', $gradeLevel);
        }

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy('grade_level')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Approve or reject a single module.
     */
    public function updateStatus($id, $status, $rejectionNote = null)
    {
        $material = $this->find($id);
        $material->status = $status;
        $material->rejection_note = ($status === 'rejected') ? $rejectionNote : null;
        $material->save();
        return $material;
    }

    /**
     * Returns teacher materials grouped by grade_level then subject for the teacher view.
     */
    public function getByTeacherAndYearGrouped($teacherId, $yearId)
    {
        $materials = $this->model
            ->with(['type', 'subject'])
            ->where('teacher_id', $teacherId)
            ->where('academic_year_id', $yearId)
            ->orderBy('grade_level')
            ->get();

        // First group by grade_level, then by subject name
        return $materials->groupBy('grade_level')->map(function ($gradeItems) {
            return $gradeItems->groupBy(function ($item) {
                return $item->subject ? $item->subject->name : 'Umum';
            });
        });
    }
}
