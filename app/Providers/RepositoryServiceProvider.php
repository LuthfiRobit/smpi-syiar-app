<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Contracts
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\AcademicYearRepositoryInterface;
use App\Repositories\Contracts\SchoolIdentityRepositoryInterface;
use App\Repositories\Contracts\SubjectRepositoryInterface;
use App\Repositories\Contracts\TeacherRepositoryInterface;
use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Repositories\Contracts\ClassroomRepositoryInterface;
use App\Repositories\Contracts\ScheduleRepositoryInterface;
use App\Repositories\Contracts\TimeSlotRepositoryInterface;

// Implementations
use App\Repositories\Eloquents\UserRepository;
use App\Repositories\Eloquents\AcademicYearRepository;
use App\Repositories\Eloquents\SchoolIdentityRepository;
use App\Repositories\Eloquents\SubjectRepository;
use App\Repositories\Eloquents\TeacherRepository;
use App\Repositories\Eloquents\StudentRepository;
use App\Repositories\Eloquents\ClassroomRepository;
use App\Repositories\Eloquents\ScheduleRepository;
use App\Repositories\Eloquents\TimeSlotRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Binding Interface ke Implementation
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AcademicYearRepositoryInterface::class, AcademicYearRepository::class);
        $this->app->bind(SchoolIdentityRepositoryInterface::class, SchoolIdentityRepository::class);
        $this->app->bind(SubjectRepositoryInterface::class, SubjectRepository::class);
        $this->app->bind(TeacherRepositoryInterface::class, TeacherRepository::class);
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);
        $this->app->bind(ClassroomRepositoryInterface::class, ClassroomRepository::class);
        $this->app->bind(ScheduleRepositoryInterface::class, ScheduleRepository::class);
        $this->app->bind(TimeSlotRepositoryInterface::class, TimeSlotRepository::class);
        $this->app->bind(\App\Repositories\Contracts\TeacherAttendanceRepositoryInterface::class, \App\Repositories\Eloquents\TeacherAttendanceRepository::class);
        $this->app->bind(\App\Repositories\Contracts\TeachingJournalRepositoryInterface::class, \App\Repositories\Eloquents\TeachingJournalRepository::class);
        $this->app->bind(\App\Repositories\Contracts\TeachingMaterialRepositoryInterface::class, \App\Repositories\Eloquents\TeachingMaterialRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
