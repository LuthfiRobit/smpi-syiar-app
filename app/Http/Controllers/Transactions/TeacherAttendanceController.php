<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\TeacherAttendanceRepositoryInterface;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TeacherAttendanceController extends Controller
{
    protected $repository;

    public function __construct(TeacherAttendanceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $user = Auth::user();

        // IF ADMIN: View all teachers status with date filter
        if ($user->role === 'admin') {
            $today = request('date', Carbon::now()->toDateString());
            $teachers = Teacher::with([
                'attendances' => function ($q) use ($today) {
                    $q->where('date', $today);
                }
            ])->get();

            return view('transactions.teacher_attendance.admin_index', compact('teachers', 'today'));
        }

        // IF TEACHER: View own status
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->back()->with('error', 'Data guru tidak ditemukan untuk akun ini.');
        }

        $todayStatus = $this->repository->getTodayStatus($teacher->id);

        return view('transactions.teacher_attendance.index', compact('teacher', 'todayStatus'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // If Admin is forcing check-in for a teacher
        if ($user->role === 'admin' && $request->has('teacher_id')) {
            return $this->adminStore($request);
        }

        // Normal Teacher Check-in
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        $date = Carbon::now('Asia/Jakarta')->toDateString();
        $time = Carbon::now('Asia/Jakarta')->format('H:i:s');

        $this->repository->checkIn($teacher->id, $date, $time);

        return redirect()->back()->with('success', 'Berhasil melakukan Check-in!');
    }

    public function update(Request $request, $id)
    {
        // $id di sini tidak terlalu dipakai jika kita pakai current user, 
        // tapi untuk konsistensi REST bisa validasi kalau $id punya user ini.
        // If Admin is forcing check-out for a teacher
        if (Auth::user()->role === 'admin') {
            // For simplicity, admin check-out logic can be here or separate
            // Getting teacher_id from the attendance ID or request is needed
            // But for now let's focus on the teacher's self check-out
        }

        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        $date = Carbon::now('Asia/Jakarta')->toDateString();
        $time = Carbon::now('Asia/Jakarta')->format('H:i:s');

        $this->repository->checkOut($teacher->id, $date, $time);

        return redirect()->back()->with('success', 'Berhasil melakukan Check-out!');
    }

    protected function adminStore(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'status' => 'required|in:Hadir,Izin,Sakit,Alpha'
        ]);

        $date = $request->input('date_filter', Carbon::now('Asia/Jakarta')->toDateString());
        $time = Carbon::now('Asia/Jakarta')->format('H:i:s');

        // If status is Hadir, set check_in time. If others, check_in is null/empty but status is set
        if ($request->status === 'Hadir') {
            $this->repository->checkIn($request->teacher_id, $date, $time);
        } else {
            // Manual adjustment via model/repo for non-present status
            \App\Models\TeacherAttendance::updateOrCreate(
                ['teacher_id' => $request->teacher_id, 'date' => $date],
                ['status' => $request->status, 'check_in' => null, 'check_out' => null]
            );
        }

        return redirect()->back()->with('success', 'Status absensi berhasil diperbarui oleh Admin.');
    }

    public function checkout($attendanceId)
    {
        $attendance = \App\Models\TeacherAttendance::findOrFail($attendanceId);
        $time = Carbon::now('Asia/Jakarta')->format('H:i:s');

        $attendance->update(['check_out' => $time]);

        return redirect()->back()->with('success', 'Checkout berhasil!');
    }
}
