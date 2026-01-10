<?php

namespace App\Repositories\Contracts;

interface TeachingJournalRepositoryInterface
{
    public function getTodaySchedule($teacherId, $dayName);
    public function getByScheduleAndDate($scheduleId, $date);
    public function storeJournalWithAttendance(array $journalData, array $attendanceData);
    public function getByDate($date);
    public function getByIdWithDetails($id);
}
