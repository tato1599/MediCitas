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
        Schema::create('meal_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()
                ->comment('References the ID of the employee associated with the meal hour.');
            $table->time('start')
                ->comment('Start time of the meal break for the employee.');
            $table->string('day', 10)
                ->comment('Day of the week for the meal hour (e.g., Monday, Tuesday). Lang must vary based on the locale.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_hours');
    }
};
