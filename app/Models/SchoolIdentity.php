<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolIdentity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'npsn',
        'address',
        'phone',
        'email',
        'website',
        'logo_path',
        'headmaster_name',
        'headmaster_nip',
    ];
}
