<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimeSlot;
use App\Models\AcademicSetting;
use App\Models\AcademicYear;
use App\Repositories\Contracts\ScheduleRepositoryInterface;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected $repository;

    public function __construct(ScheduleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        // Get all academic years for filter
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        // Get active year
        $activeYear = AcademicYear::where('is_active', true)->first();

        // Initial classrooms (filtered by active year if exists)
        $classrooms = $activeYear ? Classroom::where('academic_year_id', $activeYear->id)->get() : collect([]);

        $subjects = Subject::all();
        $teachers = Teacher::all();

        return view('academic.schedule.index', compact('academicYears', 'activeYear', 'classrooms', 'subjects', 'teachers'));
    }

    public function data(Request $request)
    {
        $classroomId = $request->query('classroom_id');
        $schedules = $this->repository->getByClassroom($classroomId);
        return response()->json($schedules);
    }

    public function matrix(Request $request, $classroomId)
    {
        // Get classroom to find its academic year
        $classroom = Classroom::findOrFail($classroomId);
        $academicYearId = $classroom->academic_year_id;

        // Get active days for this year
        $activeDays = AcademicSetting::get('active_days', [], $academicYearId);

        // If no active days found (maybe because settings not set), default to Monday-Saturday
        if (empty($activeDays)) {
            // Default fallback or handle error. Let's return empty matrix or standard days?
            // Ideally should return empty if settings are strictly required.
        }

        $matrix = $this->repository->getScheduleMatrix($classroomId, $academicYearId, $activeDays);

        // Return both matrix and active_days so frontend knows what columns to render
        return response()->json([
            'matrix' => $matrix,
            'active_days' => $activeDays
        ]);
    }

    public function store(Request $request)
    {
        // Validation with academic_year_id from request
        $academicYearId = $request->academic_year_id;
        $activeDays = AcademicSetting::get('active_days', [], $academicYearId);
        $activeDaysStr = implode(',', $activeDays);

        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'day' => 'required|in:' . $activeDaysStr,
            'schedules.*.time_slot_id' => [
                'required',
                'exists:time_slots,id',
                function ($attribute, $value, $fail) use ($request) {
                    $timeSlot = TimeSlot::find($value);
                    if ($timeSlot && $timeSlot->day !== $request->day) {
                        $fail('Jam pelajaran tidak sesuai dengan hari yang dipilih.');
                    }
                },
            ],
            'schedules.*.subject_id' => 'required|exists:subjects,id',
            'schedules.*.teacher_id' => 'required|exists:teachers,id',
        ]);

        try {
            if ($request->has('schedules')) {
                // Bulk Create
                $this->repository->createBulk($request->all());
            } else {
                // Single Create (if needed, but usually bulk covers it or different endpoint)
                // Assuming single create follows similar pattern or is handled by same method with different payload structure
                // But the form sends 'schedules' array even for single row usually, let's stick to createBulk logic
            }

            return response()->json(['message' => 'Jadwal berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'day' => 'required',
            'time_slot_id' => 'required|exists:time_slots,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        try {
            $this->repository->update($id, $request->all());
            return response()->json(['message' => 'Jadwal berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(['message' => 'Jadwal berhasil dihapus']);
    }

    public function checkAvailability(Request $request)
    {
        $academicYearId = $request->academic_year_id;

        // Robustness: If academic_year_id is not provided, try to find active one or throw error
        if (!$academicYearId) {
            $activeYear = AcademicYear::where('is_active', true)->first();
            $academicYearId = $activeYear ? $activeYear->id : null;
        }

        if (!$academicYearId) {
            return response()->json([
                'available' => false,
                'message' => 'Tahun ajaran tidak valid atau tidak aktif'
            ]);
        }

        $teacherConflict = $this->repository->checkTeacherAvailability(
            $request->teacher_id,
            $request->day,
            $request->time_slot_id,
            $academicYearId,
            $request->schedule_id ?? null
        );

        $classConflict = $this->repository->checkClassAvailability(
            $request->classroom_id,
            $request->day,
            $request->time_slot_id,
            $academicYearId,
            $request->schedule_id ?? null
        );

        $available = !$teacherConflict && !$classConflict;
        $message = 'Jadwal tersedia';

        if ($teacherConflict) {
            $message = "Guru " . $teacherConflict->teacher->name . " sudah mengajar mata pelajaran " . $teacherConflict->subject->name . " di kelas " . $teacherConflict->classroom->name . " pada hari dan jam yang sama.";
        } elseif ($classConflict) {
            $message = "Kelas " . $classConflict->classroom->name . " sudah memiliki jadwal mata pelajaran " . $classConflict->subject->name . " bersama " . $classConflict->teacher->name . " pada hari dan jam yang sama.";
        }

        return response()->json([
            'available' => $available,
            'teacher_conflict' => (bool) $teacherConflict,
            'class_conflict' => (bool) $classConflict,
            'message' => $message
        ]);
    }

    public function getTimeSlots(Request $request, $day)
    {
        $yearId = $request->query('year_id');
        $activeDays = AcademicSetting::get('active_days', [], $yearId);

        if (!in_array($day, $activeDays)) {
            // If the day is NOT active for the specified year, return empty
            // This prevents selecting slots for a day that shouldn't exist in that year
            return response()->json([]);
        }

        $timeSlots = TimeSlot::forDay($day)->get();
        return response()->json($timeSlots);
    }

    public function getClassrooms(Request $request)
    {
        $yearId = $request->query('year_id');
        $classrooms = Classroom::where('academic_year_id', $yearId)->orderBy('name')->get();
        return response()->json($classrooms);
    }

    public function getActiveDays(Request $request)
    {
        $yearId = $request->query('year_id');
        $activeDays = AcademicSetting::get('active_days', [], $yearId);
        return response()->json($activeDays);
    }
}
