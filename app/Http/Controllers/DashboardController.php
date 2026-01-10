<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data = [];

        if ($user->role === 'admin') {
            $data['total_teachers'] = \App\Models\Teacher::count();
            $data['total_students'] = \App\Models\Student::count();
            $data['total_classrooms'] = \App\Models\Classroom::count();

            // Today's Teacher Attendance
            $today = \Carbon\Carbon::now()->toDateString();
            $totalTeachers = $data['total_teachers'];
            $presentTeachers = \App\Models\TeacherAttendance::where('date', $today)
                ->where('status', 'Hadir')
                ->count();

            $data['attendance_percentage'] = $totalTeachers > 0
                ? round(($presentTeachers / $totalTeachers) * 100)
                : 0;
        }

        if ($user->role === 'teacher') {
            $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();

            if ($teacher) {
                $today = \Carbon\Carbon::now('Asia/Jakarta')->toDateString();

                // My Attendance Today
                $data['today_attendance'] = \App\Models\TeacherAttendance::where('teacher_id', $teacher->id)
                    ->where('date', $today)
                    ->first();

                // My Schedules Today
                $schedules = \App\Repositories\Eloquents\ScheduleRepository::class;
                // Note: We should ideally use the repository via DI, but for dashboard simple query is fine or instantiate repo.
                // Or simplified direct query:
                $data['today_schedules_count'] = \App\Models\Schedule::where('teacher_id', $teacher->id)
                    ->where('day', \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l'))
                    ->count();

                // Journals Filled Today
                $data['journals_filled'] = \App\Models\TeachingJournal::whereHas('schedule', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })
                    ->where('date', $today)
                    ->count();
            }
        }

        return view('dashboard.index', compact('user', 'data'));
    }
}
