<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PicketRepositoryInterface;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\AcademicSetting;
use Illuminate\Http\Request;

class PicketScheduleController extends Controller
{
    protected $repository;

    public function __construct(PicketRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $activeYear = AcademicYear::where('is_active', '=', true)->firstOrFail();
        $teachers = Teacher::orderBy('name', 'asc')->get();
        $schedules = $this->repository->getSchedules($activeYear->id);
        $days = AcademicSetting::get('active_days', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'], $activeYear->id);

        return view('masters.picket_schedule.index', compact('activeYear', 'teachers', 'schedules', 'days'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'day' => 'required',
            'academic_year_id' => 'required|exists:academic_years,id'
        ]);

        $this->repository->storeSchedule($request->all());

        return redirect()->back()->with('success', 'Jadwal piket berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $this->repository->deleteSchedule($id);
        return redirect()->back()->with('success', 'Jadwal piket berhasil dihapus');
    }
}
