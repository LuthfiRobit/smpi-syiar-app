<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\SubjectRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    protected $repository;

    public function __construct(SubjectRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return view('masters.subject.index');
    }

    public function data()
    {
        $subjects = $this->repository->getAll();
        return response()->json($subjects);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:subjects,code',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->repository->create($request->all());
            return response()->json(['message' => 'Mata pelajaran berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menambahkan data: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:20|unique:subjects,code,' . $id,
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $this->repository->update($id, $request->all());
            return response()->json(['message' => 'Mata pelajaran berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->repository->delete($id);
            return response()->json(['message' => 'Mata pelajaran berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}
