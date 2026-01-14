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
use Illuminate\Support\Facades\DB;
use App\Models\TeachingJournal;

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

        Carbon::setLocale('id');
        $todayName = Carbon::now()->translatedFormat('l');
        $todayDate = Carbon::now()->toDateString();

        $schedules = $this->journalRepository->getTodaySchedule($teacher->id, $todayName);

        // Grouping Logic
        $groupedSchedules = [];
        $currentGroup = null;

        foreach ($schedules as $schedule) {
            $schedule->journal = $this->journalRepository->getByScheduleAndDate($schedule->id, $todayDate);

            // Check if we can group with previous
            if (
                $currentGroup &&
                $currentGroup['subject_id'] == $schedule->subject_id &&
                $currentGroup['classroom_id'] == $schedule->classroom_id
            ) {

                // Add to current group
                $currentGroup['items'][] = $schedule;
                $currentGroup['schedule_ids'][] = $schedule->id;
                $currentGroup['end_time'] = $schedule->timeSlot->end_time;
                $currentGroup['time_slot_names'][] = $schedule->timeSlot->name;

                // If any schedule in group has journal, mark as filled (assuming consistency)
                if ($schedule->journal) {
                    $currentGroup['is_filled'] = true;
                    $currentGroup['journal_status'] = $schedule->journal->status;
                }
            } else {
                // Save previous group if exists
                if ($currentGroup) {
                    $groupedSchedules[] = $currentGroup;
                }

                // Start new group
                $currentGroup = [
                    'subject_id' => $schedule->subject_id,
                    'classroom_id' => $schedule->classroom_id,
                    'subject_name' => $schedule->subject->name,
                    'classroom_name' => $schedule->classroom->name,
                    'start_time' => $schedule->timeSlot->start_time,
                    'end_time' => $schedule->timeSlot->end_time,
                    'time_slot_names' => [$schedule->timeSlot->name],
                    'items' => [$schedule],
                    'schedule_ids' => [$schedule->id],
                    'is_filled' => $schedule->journal ? true : false,
                    'journal_status' => $schedule->journal ? $schedule->journal->status : null,
                    'journal_id' => $schedule->journal ? $schedule->journal->id : null // Store first journal ID for edit link
                ];
            }
        }

        // Add last group
        if ($currentGroup) {
            $groupedSchedules[] = $currentGroup;
        }

        return view('transactions.teaching_journal.index', compact('groupedSchedules', 'todayName', 'todayDate'));
    }

    public function create($scheduleIds)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // VALIDATION: Must check-in first
        $attendance = $this->attendanceRepository->getTodayStatus($teacher->id);
        if (!$attendance || !$attendance->check_in) {
            return redirect()->route('transactions.teacher-attendance.index')
                ->with('error', 'Silakan absen check-in terlebih dahulu sebelum mengisi jurnal!');
        }

        // Handle multiple IDs (comma separated)
        $ids = explode(',', $scheduleIds);
        $schedules = Schedule::with(['classroom', 'subject', 'timeSlot'])->whereIn('id', $ids)->get();

        if ($schedules->isEmpty()) {
            abort(404, 'Jadwal tidak ditemukan');
        }

        // Security & Consistency Check
        $firstSchedule = $schedules->first();
        foreach ($schedules as $schedule) {
            if ($schedule->teacher_id != $teacher->id) {
                abort(403, 'Unauthorized access to this schedule.');
            }
            if ($schedule->classroom_id != $firstSchedule->classroom_id || $schedule->subject_id != $firstSchedule->subject_id) {
                abort(400, 'Invalid schedule grouping');
            }
        }

        $students = $this->studentRepository->getByClassroom($firstSchedule->classroom_id);

        // Pass the first schedule for context, and all IDs for the form
        return view('transactions.teaching_journal.form', [
            'schedule' => $firstSchedule,
            'students' => $students,
            'schedule_ids' => $scheduleIds,
            'grouped_schedules' => $schedules
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_ids' => 'required|string', // Comma separated IDs
            'topic' => 'required|string',
            'description' => 'nullable|string',
            'attendance' => 'required|array',
        ]);

        $ids = explode(',', $request->schedule_ids);
        $date = Carbon::now()->toDateString();

        DB::transaction(function () use ($request, $ids, $date) {
            foreach ($ids as $scheduleId) {
                // Check if already filled to prevent duplicates
                $exists = $this->journalRepository->getByScheduleAndDate($scheduleId, $date);
                if ($exists)
                    continue;

                // Prepare Journal Data
                $journalData = [
                    'schedule_id' => $scheduleId,
                    'date' => $date,
                    'topic' => $request->topic,
                    'description' => $request->description,
                    'status' => 'Submitted',
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
            }
        });

        return redirect()->route('transactions.journals.index')->with('success', 'Jurnal dan Absensi berhasil disimpan untuk semua jam pelajaran terkait.');
    }

    public function edit($id)
    {
        $journal = TeachingJournal::with(['schedule.classroom', 'schedule.subject', 'schedule.timeSlot', 'studentAttendances'])->findOrFail($id);

        // Check access
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher || $journal->schedule->teacher_id != $teacher->id) {
            abort(403);
        }

        if ($journal->status == 'Approved') {
            return redirect()->back()->with('error', 'Jurnal yang sudah disetujui tidak dapat diedit.');
        }

        // Find sibling journals (Same date, class, subject) for Group Edit
        $siblingJournals = TeachingJournal::where('date', $journal->date)
            ->whereHas('schedule', function ($q) use ($journal) {
                $q->where('classroom_id', $journal->schedule->classroom_id)
                    ->where('subject_id', $journal->schedule->subject_id)
                    ->where('teacher_id', $journal->schedule->teacher_id);
            })
            ->get();

        $students = $this->studentRepository->getByClassroom($journal->schedule->classroom_id);

        return view('transactions.teaching_journal.edit', compact('journal', 'siblingJournals', 'students'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'topic' => 'required|string',
            'description' => 'nullable|string',
            'attendance' => 'required|array',
        ]);

        $journal = TeachingJournal::findOrFail($id);

        // Authorization check
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher || $journal->schedule->teacher_id != $teacher->id) {
            abort(403);
        }

        if ($journal->status == 'Approved') {
            return back()->with('error', 'Jurnal sudah disetujui.');
        }

        // Find sibling journals to update all of them
        $siblingJournals = TeachingJournal::where('date', $journal->date)
            ->whereHas('schedule', function ($q) use ($journal) {
                $q->where('classroom_id', $journal->schedule->classroom_id)
                    ->where('subject_id', $journal->schedule->subject_id)
                    ->where('teacher_id', $journal->schedule->teacher_id);
            })
            ->get();

        DB::transaction(function () use ($request, $siblingJournals) {
            foreach ($siblingJournals as $sibling) {
                // Update Journal
                $sibling->update([
                    'topic' => $request->topic,
                    'description' => $request->description,
                    'status' => 'Submitted', // Reset to submitted if it was draft
                ]);

                // Update Attendance
                foreach ($request->attendance as $studentId => $status) {
                    $attendance = \App\Models\StudentAttendance::where('teaching_journal_id', $sibling->id)
                        ->where('student_id', $studentId)
                        ->first();

                    if ($attendance) {
                        $attendance->update([
                            'status' => $status,
                            'note' => $request->note[$studentId] ?? null
                        ]);
                    } else {
                        // Create if missing (edge case)
                        $sibling->studentAttendances()->create([
                            'student_id' => $studentId,
                            'status' => $status,
                            'note' => $request->note[$studentId] ?? null
                        ]);
                    }
                }
            }
        });

        return redirect()->route('transactions.journals.index')->with('success', 'Jurnal berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Draft,Submitted,Approved'
        ]);

        $journal = TeachingJournal::findOrFail($id);

        // Find sibling journals to update all status
        // Only if they belong to the same session group logic
        $siblingJournals = TeachingJournal::where('date', $journal->date)
            ->whereHas('schedule', function ($q) use ($journal) {
                $q->where('classroom_id', $journal->schedule->classroom_id)
                    ->where('subject_id', $journal->schedule->subject_id)
                    ->where('teacher_id', $journal->schedule->teacher_id);
            })
            ->get();

        foreach ($siblingJournals as $sibling) {
            $sibling->update(['status' => $request->status]);
        }

        // If AJAX request
        if ($request->ajax()) {
            return response()->json(['message' => 'Status berhasil diperbarui']);
        }

        return back()->with('success', 'Status jurnal berhasil diperbarui');
    }

    // Admin Methods
    public function adminIndex(Request $request)
    {
        $date = $request->input('date', Carbon::now()->toDateString());
        $allJournals = $this->journalRepository->getByDate($date);

        // Grouping Logic for Admin
        // Similar to teacher, but we group by Teacher + Class + Subject
        $groupedJournals = [];
        $currentGroup = null;

        foreach ($allJournals as $journal) {
            // Check if can group with previous
            if (
                $currentGroup &&
                $currentGroup['teacher_id'] == $journal->schedule->teacher_id &&
                $currentGroup['classroom_id'] == $journal->schedule->classroom_id &&
                $currentGroup['subject_id'] == $journal->schedule->subject_id
            ) {
                // Add to current group
                $currentGroup['items'][] = $journal;
                $currentGroup['end_time'] = $journal->schedule->timeSlot->end_time;
                $currentGroup['time_slot_names'][] = $journal->schedule->timeSlot->name;
            } else {
                // Save previous
                if ($currentGroup) {
                    $groupedJournals[] = $currentGroup;
                }

                // Start new group
                $currentGroup = [
                    'id' => $journal->id, // Use first journal ID for links
                    'teacher_id' => $journal->schedule->teacher_id,
                    'classroom_id' => $journal->schedule->classroom_id,
                    'subject_id' => $journal->schedule->subject_id,
                    'teacher_name' => $journal->schedule->teacher->name,
                    'classroom_name' => $journal->schedule->classroom->name,
                    'subject_name' => $journal->schedule->subject->name,
                    'topic' => $journal->topic,
                    'status' => $journal->status,
                    'start_time' => $journal->schedule->timeSlot->start_time,
                    'end_time' => $journal->schedule->timeSlot->end_time,
                    'time_slot_names' => [$journal->schedule->timeSlot->name],
                    'items' => [$journal]
                ];
            }
        }

        if ($currentGroup) {
            $groupedJournals[] = $currentGroup;
        }

        return view('transactions.teaching_journal.admin_index', compact('groupedJournals', 'date'));
    }

    public function show($id)
    {
        $journal = $this->journalRepository->getByIdWithDetails($id);

        // Find sibling journals for consolidated view
        $siblingJournals = TeachingJournal::where('date', $journal->date)
            ->whereHas('schedule', function ($q) use ($journal) {
                $q->where('classroom_id', $journal->schedule->classroom_id)
                    ->where('subject_id', $journal->schedule->subject_id)
                    ->where('teacher_id', $journal->schedule->teacher_id);
            })
            ->get();

        return view('transactions.teaching_journal.show', compact('journal', 'siblingJournals'));
    }
}
