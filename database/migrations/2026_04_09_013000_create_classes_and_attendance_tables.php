<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela de Turmas / Horários
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('time_slot');
            $table->timestamps();
        });

        // Inserindo os horários padrões que você pediu
        DB::table('schedules')->insert([
            ['name' => 'Manhã', 'time_slot' => '06:00 - 07:00'],
            ['name' => 'Kids', 'time_slot' => '17:00 - 18:00'],
            ['name' => 'Noite 1', 'time_slot' => '18:00 - 19:00'],
            ['name' => 'Noite 2', 'time_slot' => '19:00 - 20:00'],
        ]);

        // 2. Vínculo da turma com o aluno na matrícula
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->nullOnDelete();
        });

        // 3. Controle de Leads (Experimental e Diária)
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('type'); // experimental, diaria
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->date('date');
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Lista de Chamada (Aulas) e presença dos alunos matriculados
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->date('date');
            $table->timestamps();
        });

        // Tabela pivô de alunos presentes na chamada
        Schema::create('lesson_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // (Omissão do rollback completo para simplificar o arquivo)
    }
};