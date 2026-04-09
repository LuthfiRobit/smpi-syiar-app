<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\PicketRepositoryInterface;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PicketAttendanceController extends Controller
{
    protected $repository;

    public function __construct(PicketRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $user = Auth::user();
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $dayName = $this->getDayName(Carbon::now('Asia/Jakarta')->dayOfWeek);

        if ($user->role === 'admin') {
            $activeYear = AcademicYear::where('is_active', '=', true)->first();
            $scheduledTeachers = $this->repository->getSchedules($activeYear->id)
                ->where('day', '=', $dayName)
                ->map(fn($s) => $s->teacher);

            $attendances = $this->repository->getAttendances($today);

            // Map scheduled teachers to their attendance or default to "Belum Hadir"
            $data = $scheduledTeachers->map(function ($teacher) use ($attendances) {
                $att = $attendances->where('teacher_id', $teacher->id)->first();
                return [
                    'teacher' => $teacher,
                    'attendance' => $att,
                    'is_scheduled' => true,
                    'status' => $att ? $att->status : 'Belum Hadir'
                ];
            });

            // Add extra picket teachers (those who attended but weren't scheduled)
            $extraAttendances = $attendances->whereNotIn('teacher_id', $scheduledTeachers->pluck('id'));
            foreach ($extraAttendances as $att) {
                $data->push([
                    'teacher' => $att->teacher,
                    'attendance' => $att,
                    'is_scheduled' => false,
                    'status' => $att->status
                ]);
            }

            $teachers = Teacher::orderBy('name', 'asc')->get();
            return view('transactions.picket_attendance.admin_index', [
                'attendances' => $data,
                'teachers' => $teachers,
                'today' => $today
            ]);
        }

        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        $activeYear = AcademicYear::where('is_active', true)->first();

        $scheduled = $this->repository->getTeacherSchedules($teacher->id, $activeYear->id)
            ->where('day', '=', $dayName)->first();

        $attendance = $this->repository->getTeacherAttendance($teacher->id, $today);

        return view('transactions.picket_attendance.index', compact('teacher', 'scheduled', 'attendance', 'today', 'dayName'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $date = Carbon::now('Asia/Jakarta')->toDateString();
        $time = Carbon::now('Asia/Jakarta')->format('H:i:s');

        if ($user->role === 'admin' && $request->has('teacher_id')) {
            $this->repository->adminUpdateAttendance([
                'teacher_id' => $request->teacher_id,
                'date' => $date,
                'status' => $request->status,
                'check_in' => $request->status === 'Hadir' ? $time : null,
                'is_extra' => true // Admin can check in anyone
            ]);
            return redirect()->back()->with('success', 'Absensi piket berhasil diperbarui');
        }

        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
        $activeYear = AcademicYear::where('is_active', true)->first();
        $dayName = $this->getDayName(Carbon::now('Asia/Jakarta')->dayOfWeek);

        $isScheduled = $this->repository->getTeacherSchedules($teacher->id, $activeYear->id)
            ->where('day', '=', $dayName)->count() > 0;

        $this->repository->checkIn($teacher->id, $date, $time, !$isScheduled);

        return redirect()->back()->with('success', 'Berhasil melakukan absensi piket');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $date = Carbon::now('Asia/Jakarta')->toDateString();
        $time = Carbon::now('Asia/Jakarta')->format('H:i:s');

        if ($user->role === 'teacher') {
            $teacher = Teacher::where('user_id', '=', $user->id)->firstOrFail();
            $this->repository->checkOut($teacher->id, $date, $time);
        }

        return redirect()->back()->with('success', 'Berhasil melakukan checkout piket');
    }

    private function getDayName($dayIndex)
    {
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu'
        ];
        return $days[$dayIndex];
    }
}
