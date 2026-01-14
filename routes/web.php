<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Masters\SchoolIdentityController;
use App\Http\Controllers\Masters\AcademicYearController;
use App\Http\Controllers\Masters\SubjectController;
use App\Http\Controllers\Masters\TimeSlotController;
use App\Http\Controllers\Masters\TeacherController;
use App\Http\Controllers\Masters\StudentController;

// Guest Routes (Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (semua role bisa akses)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // User Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Admin Only Routes
    Route::middleware(['role:admin'])->prefix('masters')->name('masters.')->group(function () {
        // Settings (School Identity & Academic Year)
        Route::get('/settings', [SchoolIdentityController::class, 'index'])->name('settings.index');
        Route::get('/settings/identity-data', [SchoolIdentityController::class, 'data'])->name('settings.identity-data');
        Route::post('/settings/identity-update', [SchoolIdentityController::class, 'update'])->name('settings.identity-update');

        Route::get('/settings/academic-years-data', [AcademicYearController::class, 'data'])->name('academic-years.data');
        Route::post('/settings/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
        Route::post('/settings/academic-years/{id}', [AcademicYearController::class, 'update'])->name('academic-years.update');
        Route::delete('/settings/academic-years/{id}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');
        Route::post('/settings/academic-years/{id}/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');

        // Academic Settings (Active Days & Holidays)
        Route::get('/settings/academic-settings', [SchoolIdentityController::class, 'getAcademicSettings'])->name('settings.academic-data');
        Route::post('/settings/update-active-days', [SchoolIdentityController::class, 'updateActiveDays'])->name('settings.update-active-days');
        Route::post('/settings/holidays', [SchoolIdentityController::class, 'storeHoliday'])->name('settings.holidays.store');
        Route::delete('/settings/holidays/{id}', [SchoolIdentityController::class, 'destroyHoliday'])->name('settings.holidays.destroy');

        // Subjects
        Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
        Route::get('/subjects/data', [SubjectController::class, 'data'])->name('subjects.data');
        Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
        Route::post('/subjects/{id}', [SubjectController::class, 'update'])->name('subjects.update');
        Route::delete('/subjects/{id}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

        // Time Slots
        Route::get('/time-slots', [TimeSlotController::class, 'index'])->name('time-slots.index');
        Route::get('/time-slots/data', [TimeSlotController::class, 'data'])->name('time-slots.data');
        Route::post('/time-slots', [TimeSlotController::class, 'store'])->name('time-slots.store');
        Route::post('/time-slots/{id}', [TimeSlotController::class, 'update'])->name('time-slots.update');
        Route::delete('/time-slots/{id}', [TimeSlotController::class, 'destroy'])->name('time-slots.destroy');

        // Teachers
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/data', [TeacherController::class, 'data'])->name('teachers.data');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::post('/teachers/{id}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{id}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
        Route::post('/teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
        Route::post('/teachers/{id}/reset-password', [TeacherController::class, 'resetPassword'])->name('teachers.reset-password');

        // Students
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/data', [StudentController::class, 'data'])->name('students.data');
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::post('/students/{id}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
        Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
        Route::post('/students/{id}/reset-password', [StudentController::class, 'resetPassword'])->name('students.reset-password');

        // Teaching Material Types
        Route::resource('teaching-material-types', \App\Http\Controllers\Masters\TeachingMaterialTypeController::class)->except(['show', 'create', 'edit']);
    });

    // Admin & Teacher Routes
    Route::middleware(['role:admin,teacher'])->prefix('academic')->name('academic.')->group(function () {
        // Classrooms (Admin only for management)
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/classrooms', [\App\Http\Controllers\Academic\ClassroomController::class, 'index'])->name('classrooms.index');
            Route::get('/classrooms/data', [\App\Http\Controllers\Academic\ClassroomController::class, 'data'])->name('classrooms.data');
            Route::post('/classrooms', [\App\Http\Controllers\Academic\ClassroomController::class, 'store'])->name('classrooms.store');
            Route::post('/classrooms/{id}', [\App\Http\Controllers\Academic\ClassroomController::class, 'update'])->name('classrooms.update');
            Route::delete('/classrooms/{id}', [\App\Http\Controllers\Academic\ClassroomController::class, 'destroy'])->name('classrooms.destroy');
            Route::post('/classrooms/{id}/assign-students', [\App\Http\Controllers\Academic\ClassroomController::class, 'assignStudents'])->name('classrooms.assign-students');
            Route::delete('/classrooms/{classroomId}/students/{studentId}', [\App\Http\Controllers\Academic\ClassroomController::class, 'removeStudent'])->name('classrooms.remove-student');
            Route::get('/classrooms/{id}/students', [\App\Http\Controllers\Academic\ClassroomController::class, 'getStudents'])->name('classrooms.students');
            Route::get('/classrooms/unassigned-students', [\App\Http\Controllers\Academic\ClassroomController::class, 'getUnassignedStudents'])->name('classrooms.unassigned-students');
        });

        // Schedules
        Route::get('/schedules', [\App\Http\Controllers\Academic\ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/data', [\App\Http\Controllers\Academic\ScheduleController::class, 'data'])->name('schedules.data');
        Route::get('/schedules/matrix/{classroomId}', [\App\Http\Controllers\Academic\ScheduleController::class, 'matrix'])->name('schedules.matrix');
        Route::get('/schedules/classrooms', [\App\Http\Controllers\Academic\ScheduleController::class, 'getClassrooms'])->name('schedules.classrooms');
        Route::get('/schedules/active-days', [\App\Http\Controllers\Academic\ScheduleController::class, 'getActiveDays'])->name('schedules.active-days');
        Route::post('/schedules/check-availability', [\App\Http\Controllers\Academic\ScheduleController::class, 'checkAvailability'])->name('schedules.check-availability');

        Route::middleware(['role:admin'])->group(function () {
            Route::post('/schedules', [\App\Http\Controllers\Academic\ScheduleController::class, 'store'])->name('schedules.store');
            Route::post('/schedules/{id}', [\App\Http\Controllers\Academic\ScheduleController::class, 'update'])->name('schedules.update');
            Route::delete('/schedules/{id}', [\App\Http\Controllers\Academic\ScheduleController::class, 'destroy'])->name('schedules.destroy');
            Route::get('/schedules/time-slots/{day}', [\App\Http\Controllers\Academic\ScheduleController::class, 'getTimeSlots'])->name('schedules.get-time-slots');
        });
    });

    // Transaction Routes (Admin & Teacher)
    Route::middleware(['role:admin,teacher'])->prefix('transactions')->name('transactions.')->group(function () {
        // Teacher Attendance
        Route::get('/teacher-attendance', [\App\Http\Controllers\Transactions\TeacherAttendanceController::class, 'index'])->name('teacher-attendance.index');
        Route::post('/teacher-attendance', [\App\Http\Controllers\Transactions\TeacherAttendanceController::class, 'store'])->name('teacher-attendance.store');
        Route::put('/teacher-attendance/{id}', [\App\Http\Controllers\Transactions\TeacherAttendanceController::class, 'update'])->name('teacher-attendance.update');
        Route::patch('/teacher-attendance/{id}/checkout', [\App\Http\Controllers\Transactions\TeacherAttendanceController::class, 'checkout'])->name('teacher-attendance.checkout');

        // Teaching Journal
        Route::get('/journals', [\App\Http\Controllers\Transactions\TeachingJournalController::class, 'index'])->name('journals.index');
        Route::get('/journals/monitoring', [\App\Http\Controllers\Transactions\TeachingJournalController::class, 'adminIndex'])->name('journals.admin-index');
        Route::get('/journals/create/{scheduleIds}', [\App\Http\Controllers\Transactions\TeachingJournalController::class, 'create'])->name('journals.create');
        Route::post('/journals', [\App\Http\Controllers\Transactions\TeachingJournalController::class, 'store'])->name('journals.store');
        Route::get('/journals/{id}/edit', [\App\Http\Controllers\Transactions\TeachingJournalController::class, 'edit'])->name('journals.edit');
        Route::put('/journals/{id}', [\App\Http\Controllers\Transactions\TeachingJournalController::class, 'update'])->name('journals.update');
        Route::patch('/journals/{id}/status', [\App\Http\Controllers\Transactions\TeachingJournalController::class, 'updateStatus'])->name('journals.update-status');
        Route::get('/journals/{id}', [\App\Http\Controllers\Transactions\TeachingJournalController::class, 'show'])->name('journals.show');

        // Teaching Materials
        Route::get('/teaching-materials', [\App\Http\Controllers\Transactions\TeachingMaterialController::class, 'index'])->name('teaching-materials.index');
        Route::post('/teaching-materials', [\App\Http\Controllers\Transactions\TeachingMaterialController::class, 'store'])->name('teaching-materials.store');
        Route::get('/teaching-materials/monitoring', [\App\Http\Controllers\Transactions\TeachingMaterialController::class, 'adminIndex'])->name('teaching-materials.admin-index');
        Route::get('/teaching-materials/monitoring/data', [\App\Http\Controllers\Transactions\TeachingMaterialController::class, 'adminData'])->name('teaching-materials.admin-data');
        Route::get('/teaching-materials/monitoring/detail', [\App\Http\Controllers\Transactions\TeachingMaterialController::class, 'adminDetail'])->name('teaching-materials.admin-detail');
    });

    // Report Routes
    Route::middleware(['role:admin,teacher'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/teacher-attendance', [\App\Http\Controllers\Report\ReportController::class, 'teacherAttendance'])->name('teacher-attendance');
        Route::get('/student-attendance', [\App\Http\Controllers\Report\ReportController::class, 'studentAttendance'])->name('student-attendance');
        Route::get('/teaching-journals', [\App\Http\Controllers\Report\ReportController::class, 'teachingJournal'])->name('teaching-journals');
    });
});