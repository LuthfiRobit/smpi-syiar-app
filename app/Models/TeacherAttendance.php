<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'date',
        'check_in',
        'check_out',
        'status', // Hadir, Izin, Sakit, Alpha, Telat
        'note'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
