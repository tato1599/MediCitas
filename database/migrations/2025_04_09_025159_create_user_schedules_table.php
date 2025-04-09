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
            $table->date('start_date')
                ->comment('The start date of the schedule assignment.');
            $table->date('end_date')
                ->comment('The end date of the schedule assignment.');
            $table->string('rrule')
                ->comment('The rule that applies to the schedule assignment. This could be a specific set of rules or guidelines that govern the schedule.');
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
