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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)
                ->comment('The name of the schedule for identification (e.g., Morning Shift, Evening Shift).');
            $table->time('start')
                ->comment('The start time of the schedule.');
            $table->time('duration')
                ->comment('The duration of the schedule.');
            $table->string('type', 50)
                ->comment('The type of schedule (e.g., meal, work, exception).');
            $table->string('color', 7)->default('#000000')
                ->comment('HEX color code for visually representing the schedule. Defaults to black.');
            $table->softDeletes()
                ->comment('Soft delete timestamp for the schedule. This allows for recovery of deleted schedules.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
