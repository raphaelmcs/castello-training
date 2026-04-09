<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['name', 'time_slot'];

    public function students() { return $this->hasMany(Student::class); }
    public function lessons() { return $this->hasMany(Lesson::class); }
    public function leads() { return $this->hasMany(Lead::class); }
}