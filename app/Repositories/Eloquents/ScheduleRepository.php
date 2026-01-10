<?php

namespace App\Repositories\Eloquents;

use App\Models\Schedule;
use App\Models\TimeSlot;
use App\Repositories\Contracts\ScheduleRepositoryInterface;

class ScheduleRepository implements ScheduleRepositoryInterface
{
    protected $model;

    public function __construct(Schedule $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model
            ->with(['classroom', 'subject', 'teacher', 'timeSlot', 'academicYear'])
            ->get();
    }

    public function findById(int $id)
    {
        return $this->model
            ->with(['classroom', 'subject', 'teacher', 'timeSlot', 'academicYear'])
            ->findOrFail($id);
    }

    public function getByClassroom(int $classroomId)
    {
        return $this->model
            ->where('classroom_id', $classroomId)
            ->with(['classroom', 'subject', 'teacher', 'timeSlot', 'academicYear'])
            ->orderBy('day')
            ->get();
    }

    public function getByTeacher(int $teacherId)
    {
        return $this->model
            ->where('teacher_id', $teacherId)
            ->with(['classroom', 'subject', 'teacher', 'timeSlot', 'academicYear'])
            ->orderBy('day')
            ->get();
    }

    public function getByDay(string $day)
    {
        return $this->model
            ->where('day', $day)
            ->with(['classroom', 'subject', 'teacher', 'timeSlot'])
            ->get();
    }

    public function checkConflict(array $data)
    {
        return $this->checkTeacherAvailability(
            $data['teacher_id'],
            $data['day'],
            $data['time_slot_id'],
            $data['academic_year_id'],
            $data['id'] ?? null
        ) || $this->checkClassAvailability(
                    $data['classroom_id'],
                    $data['day'],
                    $data['time_slot_id'],
                    $data['academic_year_id'],
                    $data['id'] ?? null
                );
    }

    public function checkTeacherAvailability(int $teacherId, string $day, int $timeSlotId, int $academicYearId, ?int $excludeId = null)
    {
        $query = $this->model
            ->with(['classroom', 'subject', 'teacher'])
            ->where('teacher_id', $teacherId)
            ->where('day', $day)
            ->where('time_slot_id', $timeSlotId)
            ->where('academic_year_id', $academicYearId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }

    public function checkClassAvailability(int $classroomId, string $day, int $timeSlotId, int $academicYearId, ?int $excludeId = null)
    {
        $query = $this->model
            ->with(['classroom', 'subject', 'teacher'])
            ->where('classroom_id', $classroomId)
            ->where('day', $day)
            ->where('time_slot_id', $timeSlotId)
            ->where('academic_year_id', $academicYearId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }

    public function getScheduleMatrix(int $classroomId, int $academicYearId, array $activeDays)
    {
        // Get all schedules for this classroom and academic year
        $schedules = $this->model
            ->where('classroom_id', $classroomId)
            ->where('academic_year_id', $academicYearId)
            ->with(['subject', 'teacher', 'timeSlot'])
            ->get();

        // Get all time slots and group by name
        $allTimeSlots = TimeSlot::orderBy('start_time')->get();

        $timeSlotGroups = $allTimeSlots->groupBy('name');
        $timeSlotGroups = $timeSlotGroups->sortBy(function ($group) {
            return $group->first()->start_time;
        });

        // Use provided active days
        $days = $activeDays;

        // Build matrix
        $matrix = [];
        foreach ($timeSlotGroups as $name => $slots) {
            $representativeSlot = $slots->first();

            $row = [
                'time_slot_name' => $name,
                'start_time' => $representativeSlot->start_time,
                'end_time' => $representativeSlot->end_time,
                'is_break' => $representativeSlot->is_break,
            ];

            foreach ($days as $day) {
                $daySlot = $slots->where('day', $day)->first();

                if ($daySlot) {
                    $schedule = $schedules->where('time_slot_id', $daySlot->id)->first();

                    if ($schedule) {
                        $row[$day] = [
                            'id' => $schedule->id,
                            'subject' => $schedule->subject->name,
                            'subject_id' => $schedule->subject_id,
                            'teacher' => $schedule->teacher->name,
                            'teacher_id' => $schedule->teacher_id,
                            'time_slot_id' => $daySlot->id,
                            'academic_year_id' => $academicYearId
                        ];
                    } else {
                        $row[$day] = [
                            'empty' => true,
                            'time_slot_id' => $daySlot->id
                        ];
                    }
                } else {
                    $row[$day] = null;
                }
            }

            $matrix[] = $row;
        }

        return array_values($matrix);
    }

    public function create(array $data)
    {
        // Check for conflicts before creating
        if ($conflict = $this->checkTeacherAvailability($data['teacher_id'], $data['day'], $data['time_slot_id'], $data['academic_year_id'])) {
            throw new \Exception("Guru " . $conflict->teacher->name . " sudah mengajar mata pelajaran " . $conflict->subject->name . " di kelas " . $conflict->classroom->name . " pada hari dan jam yang sama.");
        }

        if ($conflict = $this->checkClassAvailability($data['classroom_id'], $data['day'], $data['time_slot_id'], $data['academic_year_id'])) {
            throw new \Exception("Kelas " . $conflict->classroom->name . " sudah memiliki jadwal mata pelajaran " . $conflict->subject->name . " bersama " . $conflict->teacher->name . " pada hari dan jam yang sama.");
        }

        return $this->model->create($data);
    }

    public function createBulk(array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $classroomId = $data['classroom_id'];
            $day = $data['day'];
            $academicYearId = $data['academic_year_id'];
            $schedules = [];

            foreach ($data['schedules'] as $index => $item) {
                if (empty($item['time_slot_id']) || empty($item['subject_id']) || empty($item['teacher_id'])) {
                    continue;
                }

                $scheduleData = [
                    'academic_year_id' => $academicYearId,
                    'classroom_id' => $classroomId,
                    'day' => $day,
                    'time_slot_id' => $item['time_slot_id'],
                    'subject_id' => $item['subject_id'],
                    'teacher_id' => $item['teacher_id'],
                ];

                // Check for duplicates within the current batch
                foreach ($data['schedules'] as $indexInner => $itemInner) {
                    if ($index !== $indexInner && $item['time_slot_id'] === $itemInner['time_slot_id']) {
                        throw new \Exception("Jam pelajaran ganda terdeteksi pada baris ke-" . ($index + 1) . " dan baris ke-" . ($indexInner + 1));
                    }
                }

                $schedules[] = $this->create($scheduleData);
            }

            return $schedules;
        });
    }

    public function update(int $id, array $data)
    {
        // Check for conflicts before updating (excluding current schedule)
        if ($conflict = $this->checkTeacherAvailability($data['teacher_id'], $data['day'], $data['time_slot_id'], $data['academic_year_id'], $id)) {
            throw new \Exception("Guru " . $conflict->teacher->name . " sudah mengajar mata pelajaran " . $conflict->subject->name . " di kelas " . $conflict->classroom->name . " pada hari dan jam yang sama.");
        }

        if ($conflict = $this->checkClassAvailability($data['classroom_id'], $data['day'], $data['time_slot_id'], $data['academic_year_id'], $id)) {
            throw new \Exception("Kelas " . $conflict->classroom->name . " sudah memiliki jadwal mata pelajaran " . $conflict->subject->name . " bersama " . $conflict->teacher->name . " pada hari dan jam yang sama.");
        }

        $schedule = $this->findById($id);
        $schedule->update($data);
        return $schedule->fresh();
    }

    public function delete(int $id)
    {
        $schedule = $this->findById($id);
        return $schedule->delete();
    }
}
