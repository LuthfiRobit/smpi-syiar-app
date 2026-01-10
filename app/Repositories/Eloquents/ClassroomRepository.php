<?php

namespace App\Repositories\Eloquents;

use App\Models\Classroom;
use App\Models\Student;
use App\Repositories\Contracts\ClassroomRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ClassroomRepository implements ClassroomRepositoryInterface
{
    protected $model;

    public function __construct(Classroom $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with(['academicYear', 'teacher'])->get();
    }

    public function findById(int $id)
    {
        return $this->model->with(['academicYear', 'teacher', 'students'])->findOrFail($id);
    }

    public function getByAcademicYear(int $academicYearId)
    {
        return $this->model->where('academic_year_id', $academicYearId)
            ->with(['teacher', 'students'])
            ->get();
    }

    public function getByGradeLevel(int $gradeLevel)
    {
        return $this->model->where('grade_level', $gradeLevel)
            ->with(['academicYear', 'teacher'])
            ->get();
    }

    public function getWithRelations()
    {
        return $this->model
            ->with(['academicYear', 'teacher'])
            ->withCount('students')
            ->get();
    }

    public function getStudents(int $classroomId)
    {
        $classroom = $this->findById($classroomId);
        return $classroom->students()->with('user')->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $classroom = $this->findById($id);
        $classroom->update($data);
        return $classroom->fresh();
    }

    public function delete(int $id)
    {
        $classroom = $this->findById($id);

        // Check if classroom has students
        if ($classroom->students()->count() > 0) {
            throw new \Exception('Tidak dapat menghapus kelas yang masih memiliki siswa');
        }

        // Check if classroom has schedules
        if ($classroom->schedules()->count() > 0) {
            throw new \Exception('Tidak dapat menghapus kelas yang masih memiliki jadwal');
        }

        return $classroom->delete();
    }

    public function assignStudents(int $classroomId, array $studentIds)
    {
        return DB::transaction(function () use ($classroomId, $studentIds) {
            // Update all selected students to this classroom
            Student::whereIn('id', $studentIds)->update([
                'classroom_id' => $classroomId
            ]);

            return true;
        });
    }
}
