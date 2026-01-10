<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Repositories\Contracts\ClassroomRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    protected $repository;
    protected $classroomRepository;

    public function __construct(
        StudentRepositoryInterface $repository,
        ClassroomRepositoryInterface $classroomRepository
    ) {
        $this->repository = $repository;
        $this->classroomRepository = $classroomRepository;
    }

    public function index()
    {
        $classrooms = $this->classroomRepository->getAll();
        return view('masters.student.index', compact('classrooms'));
    }

    public function data(Request $request)
    {
        $filters = [
            'classroom_id' => $request->classroom_id,
            'status' => $request->status
        ];
        $students = $this->repository->getPaginated(10, $filters);
        return response()->json($students);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|max:50|unique:students,nis|unique:users,username',
            'nisn' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'gender' => 'nullable|in:L,P',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->repository->create($request->all());
            return response()->json(['message' => 'Siswa berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan data: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $student = $this->repository->findById($id);
        $userId = $student->user_id;

        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|max:50|unique:students,nis,' . $id . '|unique:users,username,' . $userId,
            'nisn' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'classroom_id' => 'nullable|exists:classrooms,id',
            'gender' => 'nullable|in:L,P',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->repository->update($id, $request->all());
            return response()->json(['message' => 'Siswa berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->repository->delete($id);
            return response()->json(['message' => 'Siswa berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $file = $request->file('file');
            $data = [];

            if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
                $header = fgetcsv($handle); // Skip header row

                while (($row = fgetcsv($handle)) !== false) {
                    if (count($row) >= 3) { // Minimal NIS, Nama, Email
                        $data[] = [
                            'nis' => $row[0],
                            'name' => $row[1],
                            'email' => $row[2],
                            'classroom_id' => !empty($row[3]) ? $row[3] : null,
                            'nisn' => $row[4] ?? null,
                            'gender' => $row[5] ?? null,
                            'phone' => $row[6] ?? null,
                            'address' => $row[7] ?? null,
                        ];
                    }
                }
                fclose($handle);
            }

            $results = $this->repository->import($data);

            return response()->json([
                'message' => "Import selesai. Berhasil: {$results['success']}, Gagal: {$results['failed']}",
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal import data: ' . $e->getMessage()], 500);
        }
    }

    public function resetPassword($id)
    {
        try {
            $this->repository->resetPassword($id);
            return response()->json(['message' => 'Password dan Username siswa berhasil direset ke NIS']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mereset password: ' . $e->getMessage()], 500);
        }
    }
}
