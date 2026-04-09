<?php

namespace App\Repositories\Eloquents;

use App\Models\PicketSchedule;
use App\Models\PicketAttendance;
use App\Repositories\Contracts\PicketRepositoryInterface;

class PicketRepository implements PicketRepositoryInterface
{
    protected $scheduleModel;
    protected $attendanceModel;

    public function __construct(PicketSchedule $scheduleModel, PicketAttendance $attendanceModel)
    {
        $this->scheduleModel = $scheduleModel;
        $this->attendanceModel = $attendanceModel;
    }

    public function getSchedules(int $academicYearId)
    {
        return $this->scheduleModel->where('academic_year_id', $academicYearId)
            ->with('teacher')
            ->get();
    }

    public function getTeacherSchedules(int $teacherId, int $academicYearId)
    {
        return $this->scheduleModel->where('teacher_id', $teacherId)
            ->where('academic_year_id', $academicYearId)
            ->get();
    }

    public function storeSchedule(array $data)
    {
        return $this->scheduleModel->create($data);
    }

    public function deleteSchedule(int $id)
    {
        return $this->scheduleModel->destroy($id);
    }

    public function getAttendances(string $date)
    {
        return $this->attendanceModel->where('date', $date)
            ->with('teacher')
            ->get();
    }

    public function getTeacherAttendance(int $teacherId, string $date)
    {
        return $this->attendanceModel->where('teacher_id', $teacherId)
            ->where('date', $date)
            ->first();
    }

    public function checkIn(int $teacherId, string $date, string $time, bool $isExtra = false)
    {
        return $this->attendanceModel->updateOrCreate(
            ['teacher_id' => $teacherId, 'date' => $date],
            [
                'check_in' => $time,
                'status' => 'Hadir',
                'is_extra' => $isExtra
            ]
        );
    }

    public function checkOut(int $teacherId, string $date, string $time)
    {
        $attendance = $this->getTeacherAttendance($teacherId, $date);
        if ($attendance) {
            $attendance->update(['check_out' => $time]);
        }
        return $attendance;
    }

    public function adminUpdateAttendance(array $data)
    {
        return $this->attendanceModel->updateOrCreate(
            ['teacher_id' => $data['teacher_id'], 'date' => $data['date']],
            [
                'status' => $data['status'] ?? 'Hadir',
                'check_in' => $data['check_in'] ?? null,
                'check_out' => $data['check_out'] ?? null,
                'note' => $data['note'] ?? null,
                'is_extra' => $data['is_extra'] ?? false
            ]
        );
    }
}
