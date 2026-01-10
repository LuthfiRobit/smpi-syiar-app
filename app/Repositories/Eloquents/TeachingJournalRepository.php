<?php

namespace App\Repositories\Eloquents;

use App\Models\TeachingJournal;
use App\Models\Schedule;
use App\Repositories\Contracts\TeachingJournalRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TeachingJournalRepository implements TeachingJournalRepositoryInterface
{
    protected $model;

    public function __construct(TeachingJournal $model)
    {
        $this->model = $model;
    }

    public function getTodaySchedule($teacherId, $dayName)
    {
        return Schedule::with(['classroom', 'subject', 'timeSlot'])
            ->where('teacher_id', $teacherId)
            ->where('day', $dayName)
            ->orderBy('time_slot_id')
            ->get();
    }

    public function getByScheduleAndDate($scheduleId, $date)
    {
        return $this->model->where('schedule_id', $scheduleId)
            ->where('date', $date)
            ->first();
    }

    public function storeJournalWithAttendance(array $journalData, array $attendanceData)
    {
        return DB::transaction(function () use ($journalData, $attendanceData) {
            // Create Journal
            $journal = $this->model->create($journalData);

            // Create Attendance Records
            foreach ($attendanceData as $attendance) {
                $journal->studentAttendances()->create($attendance);
            }

            return $journal;
        });
    }

    public function getByDate($date)
    {
        return $this->model->with([
            'schedule.teacher',
            'schedule.classroom',
            'schedule.subject',
            'schedule.timeSlot'
        ])
            ->where('date', $date)
            ->latest()
            ->get();
    }

    public function getByIdWithDetails($id)
    {
        return $this->model->with([
            'schedule.teacher',
            'schedule.classroom',
            'schedule.subject',
            'schedule.timeSlot',
            'studentAttendances.student'
        ])->findOrFail($id);
    }
}
