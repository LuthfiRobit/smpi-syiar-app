<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\StudentAttendance;
use App\Models\TeachingJournal;
use App\Models\Classroom;
use App\Models\Subject;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function teacherAttendance(Request $request)
    {
        $user = Auth::user();

        // Filter Parameters
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Base Query
        $query = Teacher::query();

        // If Teacher, only see own data
        if ($user->role === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
            $query->where('id', $teacher->id);
        }

        $teachers = $query->with([
            'attendances' => function ($q) use ($month, $year) {
                $q->whereMonth('date', $month)
                    ->whereYear('date', $year)
                    ->orderBy('date');
            }
        ])->get();

        // Calculate summary stats per teacher
        foreach ($teachers as $teacher) {
            $teacher->stats = [
                'hadir' => $teacher->attendances->where('status', 'Hadir')->count(),
                'izin' => $teacher->attendances->where('status', 'Izin')->count(),
                'sakit' => $teacher->attendances->where('status', 'Sakit')->count(),
                'alpha' => $teacher->attendances->where('status', 'Alpha')->count(),
                'telat' => $teacher->attendances->where('status', 'Telat')->count(),
            ];
        }

        return view('reports.teacher_attendance.index', compact('teachers', 'month', 'year'));
    }

    public function studentAttendance(Request $request)
    {
        // Dropdowns
        $academicYears = \App\Models\AcademicYear::all();
        $activeYearId = \App\Models\AcademicYear::where('is_active', true)->value('id');
        $academicYearId = $request->input('academic_year_id', $activeYearId);

        $classrooms = Classroom::where('academic_year_id', $academicYearId)->get();
        $subjects = Subject::all();

        // Filter Paramters
        $classroomId = $request->input('classroom_id');
        $subjectId = $request->input('subject_id');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $attendances = collect([]);

        if ($classroomId) {
            $query = StudentAttendance::with(['student', 'teachingJournal.schedule.subject', 'teachingJournal.schedule.classroom'])
                ->whereHas('teachingJournal', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                })
                ->whereHas('student', function ($q) use ($classroomId) {
                    $q->where('classroom_id', $classroomId);
                });

            if ($subjectId) {
                $query->whereHas('teachingJournal.schedule', function ($q) use ($subjectId) {
                    $q->where('subject_id', $subjectId);
                });
            }

            $attendances = $query->get()->groupBy('student_id');
        }

        return view('reports.student_attendance.index', compact('academicYears', 'academicYearId', 'classrooms', 'subjects', 'attendances', 'classroomId', 'subjectId', 'startDate', 'endDate'));
    }

    public function teachingJournal(Request $request)
    {
        $user = Auth::user();

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $query = TeachingJournal::with(['schedule.classroom', 'schedule.subject', 'schedule.teacher'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc');

        if ($user->role === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->firstOrFail();
            $query->whereHas('schedule', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            });
        }

        $journals = $query->get();

        return view('reports.teaching_journal.index', compact('journals', 'startDate', 'endDate'));
    }
}
