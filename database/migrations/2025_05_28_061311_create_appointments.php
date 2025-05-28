<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointment_types', function (Blueprint $table) {
            $table->id()
                ->comment('Primary key of the appointment_types table.');
            $table->string('name')
                ->comment('Name of the appointment type (e.g., Consultation, Surgery).');
            $table->integer('duration')->nullable()
                ->comment('Default duration of the appointment type in minutes. Nullable if not specified.');
            $table->timestamps();
            $table->comment('Table to define different types of appointments and their default durations.');
        });

        DB::unprepared("
            DO $$
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'appointment_status') THEN
                    CREATE TYPE appointment_status AS ENUM ('RE', 'PR', 'AU', 'CA', 'CO', 'RP');
                END IF;
            END
            $$;
            -- appointment_status: Enum type to track appointment statuses.
            -- RE = Completed (Realizado)
            -- PR = Programmed (Agendado)
            -- AU = Ausente (No-show)
            -- CA = Canceled (Cancelado)
            -- CO = Confirmed (Confirmado)
            -- RP = Reprogrammed (Rescheduled)
        ");

        Schema::create('appointments', function (Blueprint $table) {
            $table->id()
                ->comment('Primary key of the appointments table.');
            $table->foreignId('patient_id')->constrained('patients')
                ->comment('References the ID of the patient associated with the appointment.');
            $table->foreignId('employee_id')->constrained('users', 'id')
                ->comment('References the ID of the employee assigned to the appointment.');
            $table->foreignId('appointment_type_id')->constrained('appointment_types')
                ->comment('References the ID of the appointment type.');
            $table->string('status', 2)
                ->comment('Status of the appointment as defined in the appointment_status enum.');
            $table->integer('duration')
                ->comment('Duration of the appointment in minutes.');
            $table->dateTime('start_time')
                ->comment('The scheduled start time of the appointment.');
            $table->dateTime('estimated_end_time')
                ->comment('The estimated end time of the appointment based on its duration.');
            $table->dateTime('real_end_time')->nullable()
                ->comment('The actual end time of the appointment. Nullable if not completed.');
            $table->text('notes')->nullable()
                ->comment('Additional notes or remarks about the appointment. Nullable if no notes are provided.');
            $table->dateTime('canceled_at')->nullable()
                ->comment('Timestamp when the appointment was canceled. Nullable if not canceled.');
            $table->foreignId('canceled_by')->nullable()->constrained('users')
                ->comment('References the user ID who canceled the appointment. Nullable if not canceled.');
            $table->dateTime('confirmed_at')->nullable()
                ->comment('Timestamp when the appointment was confirmed. Nullable if not confirmed.');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')
                ->comment('References the user ID who confirmed the appointment. Nullable if not confirmed.');
            $table->dateTime('no_showed_at')->nullable()
                ->comment('Timestamp when the appointment was marked as no-show. Nullable if not applicable.');
            $table->foreignId('no_showed_by')->nullable()->constrained('users')
                ->comment('References the user ID who marked the appointment as no-show. Nullable if not applicable.');
            $table->dateTime('reprogrammed_at')->nullable()
                ->comment('Timestamp when the appointment was reprogrammed. Nullable if not applicable.');
            $table->foreignId('reprogrammed_by')->nullable()->constrained('users')
                ->comment('References the user ID who reprogrammed the appointment. Nullable if not applicable.');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users')
                ->comment('References the user ID who created the appointment record.');
            $table->foreignId('updated_by')->nullable()->constrained('users')
                ->comment('References the user ID who last updated the appointment record. Nullable if never updated.');
            $table->comment('Table to track patient appointments, their statuses, and associated actions like cancellations or reprogramming.');
        });

        DB::unprepared('
            ALTER TABLE appointments
            ALTER COLUMN status TYPE appointment_status
            USING status::appointment_status;
        ');

        DB::unprepared("
            ALTER TABLE appointments ALTER COLUMN status SET DEFAULT 'PR';
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
