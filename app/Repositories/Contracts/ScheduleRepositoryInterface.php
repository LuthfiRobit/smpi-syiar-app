<?php

namespace App\Repositories\Contracts;

interface ScheduleRepositoryInterface
{
    public function getAll();
    public function findById(int $id);
    public function getByClassroom(int $classroomId);
    public function getByTeacher(int $teacherId);
    public function getByDay(string $day);
    public function checkConflict(array $data);
    public function create(array $data);
    public function createBulk(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function checkTeacherAvailability(int $teacherId, string $day, int $timeSlotId, int $academicYearId, ?int $excludeId = null);
    public function checkClassAvailability(int $classroomId, string $day, int $timeSlotId, int $academicYearId, ?int $excludeId = null);
    public function getScheduleMatrix(int $classroomId, int $academicYearId, array $activeDays);
}
