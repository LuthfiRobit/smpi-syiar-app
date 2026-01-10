<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\TeacherRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    protected $repository;

    public function __construct(TeacherRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return view('masters.teacher.index');
    }

    public function data(Request $request)
    {
        $filters = [
            'status' => $request->status
        ];
        $teachers = $this->repository->getPaginated(10, $filters);
        return response()->json($teachers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'required|string|max:50|unique:teachers,nip|unique:users,username',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'gender' => 'nullable|in:L,P',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->repository->create($request->all());
            return response()->json(['message' => 'Guru berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan data: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $teacher = $this->repository->findById($id);
        $userId = $teacher->user_id;

        $validator = Validator::make($request->all(), [
            'nip' => 'required|string|max:50|unique:teachers,nip,' . $id . '|unique:users,username,' . $userId,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'gender' => 'nullable|in:L,P',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->repository->update($id, $request->all());
            return response()->json(['message' => 'Guru berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->repository->delete($id);
            return response()->json(['message' => 'Guru berhasil dihapus']);
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
                    if (count($row) >= 3) { // Minimal NIP, Nama, Email
                        $data[] = [
                            'nip' => $row[0],
                            'name' => $row[1],
                            'email' => $row[2],
                            'phone' => $row[3] ?? null,
                            'address' => $row[4] ?? null,
                            'gender' => $row[5] ?? null,
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
            return response()->json(['message' => 'Password dan Username guru berhasil direset ke NIP']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mereset password: ' . $e->getMessage()], 500);
        }
    }
}
