<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicHoliday extends Model
{
    protected $fillable = ['academic_year_id', 'name', 'start_date', 'end_date', 'is_recurring', 'description'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Scope to find holidays overlapping with a date range
     */
    public function scopeOverlapping($query, $start, $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
                ->orWhereBetween('end_date', [$start, $end])
                ->orWhere(function ($q2) use ($start, $end) {
                    $q2->where('start_date', '<=', $start)
                        ->where('end_date', '>=', $end);
                });
        });
    }
}
