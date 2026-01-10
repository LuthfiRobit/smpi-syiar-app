<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AcademicYearRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicYearController extends Controller
{
    protected $repository;

    public function __construct(AcademicYearRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function data()
    {
        $years = $this->repository->getAll();
        return response()->json($years);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|string|max:10',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = [
                'name' => $request->year,
                'semester' => $request->semester,
                'is_active' => false
            ];
            $this->repository->create($data);
            return response()->json(['message' => 'Tahun ajaran berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan data: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|string|max:10',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = [
                'name' => $request->year,
                'semester' => $request->semester
            ];
            $this->repository->update($id, $data);
            return response()->json(['message' => 'Tahun ajaran berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->repository->delete($id);
            return response()->json(['message' => 'Tahun ajaran berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }

    public function setActive($id)
    {
        try {
            $this->repository->setActive($id);
            return response()->json(['message' => 'Tahun ajaran berhasil diaktifkan']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengaktifkan tahun ajaran: ' . $e->getMessage()], 500);
        }
    }
}
