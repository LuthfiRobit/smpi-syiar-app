<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'teaching_journal_id',
        'student_id',
        'status', // Hadir, Izin, Sakit, Alpha, Dispensasi
        'note'
    ];

    public function teachingJournal()
    {
        return $this->belongsTo(TeachingJournal::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
