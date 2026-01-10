<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\SchoolIdentityRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SchoolIdentityController extends Controller
{
    protected $repository;

    public function __construct(SchoolIdentityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        // View pengaturan menggabungkan Identitas dan Tahun Ajaran
        $academicYears = \App\Models\AcademicYear::orderBy('name', 'desc')->get();
        return view('masters.settings.index', compact('academicYears'));
    }

    public function data()
    {
        $identity = $this->repository->get();
        return response()->json($identity);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'npsn' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'website' => 'nullable|string|max:255',
            'headmaster_name' => 'required|string|max:255',
            'headmaster_nip' => 'nullable|string|max:30',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->only([
                'name',
                'npsn',
                'email',
                'phone',
                'address',
                'website',
                'headmaster_name',
                'headmaster_nip'
            ]);

            if ($request->hasFile('logo')) {
                $identity = $this->repository->get();
                if ($identity && $identity->logo_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($identity->logo_path);
                }

                $path = $request->file('logo')->store('school', 'public');
                $data['logo_path'] = $path;
            }

            $this->repository->update($data);
            return response()->json(['message' => 'Identitas sekolah berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }
    public function updateActiveDays(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'active_days' => 'required|array',
            'active_days.*' => 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
        ]);

        \App\Models\AcademicSetting::updateOrCreate(
            [
                'academic_year_id' => $request->academic_year_id,
                'key' => 'active_days'
            ],
            ['value' => $request->active_days]
        );

        return response()->json(['message' => 'Hari aktif sekolah berhasil diperbarui']);
    }

    public function getAcademicSettings(Request $request)
    {
        $yearId = $request->query('year_id');

        if (!$yearId) {
            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
            $yearId = $activeYear ? $activeYear->id : null;
        }

        if (!$yearId) {
            return response()->json([
                'active_days' => [],
                'holidays' => [],
                'year_name' => 'Belum ada tahun ajaran active'
            ]);
        }

        $activeDays = \App\Models\AcademicSetting::get('active_days', [], $yearId);
        $holidays = \App\Models\AcademicHoliday::where('academic_year_id', $yearId)
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'active_days' => $activeDays,
            'holidays' => $holidays,
            'year_id' => $yearId
        ]);
    }

    public function storeHoliday(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_recurring' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        \App\Models\AcademicHoliday::create($request->all());
        return response()->json(['message' => 'Hari libur berhasil ditambahkan']);
    }

    public function destroyHoliday($id)
    {
        \App\Models\AcademicHoliday::destroy($id);
        return response()->json(['message' => 'Hari libur berhasil dihapus']);
    }
}
