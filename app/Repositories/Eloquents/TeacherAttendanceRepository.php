<?php

namespace App\Repositories\Eloquents;

use App\Models\TeacherAttendance;
use App\Repositories\Contracts\TeacherAttendanceRepositoryInterface;
use Carbon\Carbon;

class TeacherAttendanceRepository implements TeacherAttendanceRepositoryInterface
{
    protected $model;

    public function __construct(TeacherAttendance $model)
    {
        $this->model = $model;
    }

    public function getTodayStatus($teacherId)
    {
        $today = Carbon::now()->toDateString();
        return $this->model->where('teacher_id', $teacherId)
            ->where('date', $today)
            ->first();
    }

    public function checkIn($teacherId, $date, $time)
    {
        return $this->model->updateOrCreate(
            ['teacher_id' => $teacherId, 'date' => $date],
            [
                'check_in' => $time,
                'status' => 'Hadir' // Default hadir, bisa diupdate logic untuk telat nanti
            ]
        );
    }

    public function checkOut($teacherId, $date, $time)
    {
        $attendance = $this->model->where('teacher_id', $teacherId)
            ->where('date', $date)
            ->first();

        if ($attendance) {
            $attendance->update(['check_out' => $time]);
            return $attendance;
        }

        return null;
    }

    public function getHistory($teacherId, $month = null, $year = null)
    {
        $query = $this->model->where('teacher_id', $teacherId);

        if ($month && $year) {
            $query->whereMonth('date', $month)->whereYear('date', $year);
        }

        return $query->orderBy('date', 'desc')->get();
    }
}
