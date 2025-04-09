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
        Schema::create('schedule_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()
                ->comment('References the ID of the employee associated with the schedule exception.');
            $table->date('date')
                ->comment('The date on which the schedule exception applies.');
            $table->time('start')
                ->comment('The start time of the schedule exception.');
            $table->time('duration')
                ->comment('The duration of the schedule exception.');
            $table->string('color', 7)->default('#000000')
                ->comment('Color code (in HEX) to visually represent the exception. Defaults to black.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_exceptions');
    }
};
