<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'department',
        'budget',
        'description',
        'director_validation',
        'budget_check',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'director_validation' => 'boolean',
        'budget_check' => 'boolean',
    ];
}