<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeachingMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'academic_year_id',
        'teaching_material_type_id',
        'subject_id',
        'grade_level',
        'file_type',
        'file_path',
        'link_url',
        'description',
        'status',
        'rejection_note',
    ];

    /**
     * Relasi ke Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Relasi ke Academic Year
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Relasi ke Type
     */
    public function type()
    {
        return $this->belongsTo(TeachingMaterialType::class, 'teaching_material_type_id');
    }

    /**
     * Relasi ke Subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
