<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')
                ->comment('References the ID of the employee associated with the schedule. Deletes the record if the employee is deleted.');
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade')
                ->comment('References the ID of the schedule assigned to the employee. Deletes the record if the schedule is deleted.');
            $table->string('day', 10)
                ->comment('Specifies the day of the week for the schedule (e.g., Monday, Tuesday).');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_schedules');
    }
};
