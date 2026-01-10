<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'classroom_id',
        'nis',
        'nisn',
        'name',
        'gender',
        'phone',
        'address',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Classroom
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relasi ke Student Attendances
     */
    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }
}
