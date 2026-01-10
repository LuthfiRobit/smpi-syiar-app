<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'name',
        'start_time',
        'end_time',
        'is_break',
    ];

    protected $casts = [
        'is_break' => 'boolean',
    ];

    /**
     * Scope a query to only include time slots for a given day.
     */
    public function scopeForDay($query, string $day)
    {
        return $query->where('day', $day)->orderBy('start_time');
    }

    /**
     * Relasi ke Schedules
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
