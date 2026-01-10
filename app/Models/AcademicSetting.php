<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicSetting extends Model
{
    protected $fillable = ['academic_year_id', 'key', 'value', 'description'];

    protected $casts = [
        'value' => 'array',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get setting value by key, optionally for a specific year
     */
    public static function get(string $key, $default = null, $yearId = null)
    {
        // If yearId not provided, try to get active year
        if (!$yearId) {
            $activeYear = AcademicYear::where('is_active', true)->first();
            $yearId = $activeYear ? $activeYear->id : null;
        }

        if (!$yearId) {
            return $default;
        }

        $setting = self::where('academic_year_id', $yearId)
            ->where('key', $key)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Check if a specific day is an active school day
     */
    public static function isActiveDay(string $day, $yearId = null): bool
    {
        $activeDays = self::get('active_days', [], $yearId);
        return in_array($day, $activeDays);
    }
}
