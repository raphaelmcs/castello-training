<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id', 'schedule_id', 'nome', 'email', 'telefone', 'data_nascimento', 'data_matricula'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_matricula' => 'date',
    ];

    protected static function booted(): void
    {
        static::created(function (Student $student) {
            // Cria a primeira mensalidade automaticamente ao matricular o aluno
            $student->payments()->create([
                'data_vencimento' => $student->data_matricula,
                'valor' => $student->plan->price ?? 0,
                'data_pagamento' => null, // Deixa como pendente
            ]);
        });
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function lessons(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Lesson::class);
    }
}
