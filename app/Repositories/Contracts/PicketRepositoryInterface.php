<?php

namespace App\Repositories\Contracts;

interface PicketRepositoryInterface
{
    public function getSchedules(int $academicYearId);
    public function getTeacherSchedules(int $teacherId, int $academicYearId);
    public function storeSchedule(array $data);
    public function deleteSchedule(int $id);

    public function getAttendances(string $date);
    public function getTeacherAttendance(int $teacherId, string $date);
    public function checkIn(int $teacherId, string $date, string $time, bool $isExtra = false);
    public function checkOut(int $teacherId, string $date, string $time);
    public function adminUpdateAttendance(array $data);
}
