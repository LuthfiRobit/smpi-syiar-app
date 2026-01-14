<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeachingMaterialType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relasi ke Teaching Materials
     */
    public function teachingMaterials()
    {
        return $this->hasMany(TeachingMaterial::class);
    }
}
