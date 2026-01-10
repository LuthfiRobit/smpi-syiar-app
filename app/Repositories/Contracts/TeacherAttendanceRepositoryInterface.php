<?php

namespace App\Repositories\Contracts;

interface TeacherAttendanceRepositoryInterface
{
    public function getTodayStatus($teacherId);
    public function checkIn($teacherId, $date, $time);
    public function checkOut($teacherId, $date, $time);
    public function getHistory($teacherId, $month = null, $year = null);
}
