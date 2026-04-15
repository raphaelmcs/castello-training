<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'level', 'skill', 'wod_type', 'wod_description', 'notes'
    ];

    protected $casts = [
        'date' => 'date',
    ];
}