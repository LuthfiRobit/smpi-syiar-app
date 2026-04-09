<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PicketAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'is_extra',
        'note',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
