<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = ['name', 'phone', 'type', 'amount_paid', 'date', 'schedule_id'];

    public function schedule() { return $this->belongsTo(Schedule::class); }
}