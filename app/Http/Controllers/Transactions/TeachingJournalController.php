<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\TeachingJournalRepositoryInterface;
use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Models\Teacher;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TeachingJournalController extends Controller
{
    protected $journalRepository;
    protected $studentRepository;
    protected $attendanceRepository;

    public function __construct(
        TeachingJournalRepositoryInterface $journalRepository,
        StudentRepositoryInterface $studentRepository,
        \App\Repositories\Contracts\TeacherAttendanceRepositoryInterface $attendanceRepository
    ) {
        $this->journalRepository = $journalRepository;
        $this->studentRepository = $studentRepository;
        $this->attendanceRepository = $attendanceRepository;
    }

    public function index()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->back()->with('error', 'Akses ditolak. Akun bukan guru.');
        }

        // Carbon locale is already set to 'id' via config/app.php, but let's be explicit just in case
        Carbon::setLocale('id');
        $todayName = Carbon::now()->translatedFormat('l'); // 'Senin', 'Selasa', etc.

        $schedules = $this->journalRepository->getTodaySchedule($teacher->id, $todayName);
        $todayDate = Carbon::now()->toDateString();

        // Attach journal status to each schedule
        foreach ($schedules as $schedule) {
            $schedule->journal = $this->journalRepository->getByScheduleAndDate($schedule->id, $todayDate);
        }

        return view('transactions.teaching_journal.index', compact('schedules', 'todayName', 'todayDate'));
    }

    public function create($scheduleId)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // VALIDATION: Must check-in first
        $attendance = $this->attendanceRepository->getTodayStatus($teacher->id);
        if (!$attendance || !$attendance->check_in) {
            return redirect()->route('transactions.teacher-attendance.index')
                ->with('error', 'Silakan absen check-in terlebih dahulu sebelum mengisi jurnal!');
        }

        $schedule = Schedule::with(['classroom', 'subject', 'timeSlot'])->findOrFail($scheduleId);

        // Security check: Ensure schedule belongs to logged in teacher
        if ($schedule->teacher_id != $teacher->id) {
            abort(403, 'Unauthorized access to this schedule.');
        }

        $students = $this->studentRepository->getByClassroom($schedule->classroom_id);

        return view('transactions.teaching_journal.form', compact('schedule', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'topic' => 'required|string',
            'description' => 'nullable|string',
            'attendance' => 'required|array', // Array of student_id => status
        ]);

        $date = Carbon::now()->toDateString();

        // Prepare Journal Data
        $journalData = [
            'schedule_id' => $request->schedule_id,
            'date' => $date,
            'topic' => $request->topic,
            'description' => $request->description,
            'status' => 'Submitted',
            // 'photo_path' => handle upload later
        ];

        // Prepare Attendance Data
        $attendanceData = [];
        foreach ($request->attendance as $studentId => $status) {
            $attendanceData[] = [
                'student_id' => $studentId,
                'status' => $status,
                'note' => $request->note[$studentId] ?? null
            ];
        }

        $this->journalRepository->storeJournalWithAttendance($journalData, $attendanceData);

        return redirect()->route('transactions.journals.index')->with('success', 'Jurnal dan Absensi berhasil disimpan.');
    }

    // Admin Methods
    public function adminIndex(Request $request)
    {
        $date = $request->input('date', Carbon::now()->toDateString());
        $journals = $this->journalRepository->getByDate($date);

        return view('transactions.teaching_journal.admin_index', compact('journals', 'date'));
    }

    public function show($id)
    {
        $journal = $this->journalRepository->getByIdWithDetails($id);
        return view('transactions.teaching_journal.show', compact('journal'));
    }
}
