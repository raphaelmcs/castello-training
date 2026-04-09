<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = ['schedule_id', 'date'];

    public function schedule() { return $this->belongsTo(Schedule::class); }
    public function students() { return $this->belongsToMany(Student::class); }
}