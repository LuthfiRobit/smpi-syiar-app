<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ClassroomRepositoryInterface;
use App\Repositories\Contracts\AcademicYearRepositoryInterface;
use App\Repositories\Contracts\TeacherRepositoryInterface;
use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassroomController extends Controller
{
    protected $repository;
    protected $academicYearRepository;
    protected $teacherRepository;
    protected $studentRepository;

    public function __construct(
        ClassroomRepositoryInterface $repository,
        AcademicYearRepositoryInterface $academicYearRepository,
        TeacherRepositoryInterface $teacherRepository,
        StudentRepositoryInterface $studentRepository
    ) {
        $this->repository = $repository;
        $this->academicYearRepository = $academicYearRepository;
        $this->teacherRepository = $teacherRepository;
        $this->studentRepository = $studentRepository;
    }

    public function index()
    {
        $academicYears = $this->academicYearRepository->getAll();
        $teachers = $this->teacherRepository->getAll();
        return view('academic.classroom.index', compact('academicYears', 'teachers'));
    }

    public function data()
    {
        $classrooms = $this->repository->getWithRelations();
        return response()->json($classrooms);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:100',
            'grade_level' => 'required|integer|min:7|max:9',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->repository->create($request->all());
            return response()->json(['message' => 'Kelas berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan data: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:100',
            'grade_level' => 'required|integer|min:7|max:9',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->repository->update($id, $request->all());
            return response()->json(['message' => 'Kelas berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->repository->delete($id);
            return response()->json(['message' => 'Kelas berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getStudents($id)
    {
        try {
            $students = $this->repository->getStudents($id);
            return response()->json($students);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengambil data siswa'], 500);
        }
    }

    public function getUnassignedStudents()
    {
        try {
            $students = $this->studentRepository->getAll()
                ->where('classroom_id', null)
                ->values();
            return response()->json($students);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengambil data siswa'], 500);
        }
    }

    public function assignStudents(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->repository->assignStudents($id, $request->student_ids);
            return response()->json(['message' => 'Siswa berhasil ditambahkan ke kelas']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan siswa: ' . $e->getMessage()], 500);
        }
    }

    public function removeStudent($classroomId, $studentId)
    {
        try {
            $student = $this->studentRepository->findById($studentId);

            if (!$student) {
                return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
            }

            if ($student->classroom_id != $classroomId) {
                return response()->json(['message' => 'Siswa tidak terdaftar di kelas ini'], 400);
            }

            // Update directly to avoid validation of other fields
            $student->classroom_id = null;
            $student->save();

            return response()->json(['message' => 'Siswa berhasil dikeluarkan dari kelas']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengeluarkan siswa: ' . $e->getMessage()], 500);
        }
    }
}
