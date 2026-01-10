<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\TimeSlotRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TimeSlotController extends Controller
{
    protected $repository;

    public function __construct(TimeSlotRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return view('masters.time-slot.index');
    }

    public function data(Request $request)
    {
        $filters = [
            'day' => $request->day,
            'type' => $request->type,
        ];
        $perPage = $request->per_page ?? 10;

        $data = $this->repository->getPaginated($filters, $perPage);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'days' => 'required|array',
            'days.*' => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'time_key' => 'required|string|max:10',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'type' => 'required|in:Pelajaran,Istirahat',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            foreach ($request->days as $day) {
                // Check if duplicate key exists for this day to avoid double entry
                $exists = \App\Models\TimeSlot::where('day', $day)
                    ->where('name', $request->time_key)
                    ->exists();

                if (!$exists) {
                    $data = $request->except('days');
                    $data['day'] = $day;
                    $data['name'] = $request->time_key;
                    $data['is_break'] = ($request->type === 'Istirahat');
                    $this->repository->create($data);
                }
            }
            DB::commit();
            return response()->json(['message' => 'Jam pelajaran berhasil ditambahkan ke hari yang dipilih']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menambahkan data: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'time_key' => 'required|string|max:10',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'type' => 'required|in:Pelajaran,Istirahat',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->all();
            $data['name'] = $request->time_key;
            $data['is_break'] = ($request->type === 'Istirahat');
            $this->repository->update($id, $data);
            return response()->json(['message' => 'Jam pelajaran berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->repository->delete($id);
            return response()->json(['message' => 'Jam pelajaran berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}
