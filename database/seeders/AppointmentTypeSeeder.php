<?php

namespace Database\Seeders;

use App\Models\AppointmentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppointmentType::create([
            'name' => 'Consulta General',
            'duration' => 30, // Duración en minutos
            // 'description' => 'Consulta médica general para evaluación y diagnóstico.',
        ]);
        AppointmentType::create([
            'name' => 'Control de Enfermedad Crónica',
            'duration' => 45, // Duración en minutos
            // 'description' => 'Seguimiento y control de enfermedades crónicas como diabetes o hipertensión.',
        ]);
        AppointmentType::create([
            'name' => 'Consulta de Especialidad',
            'duration' => 60, // Duración en minutos
            // 'description' => 'Consulta con un especialista para evaluación y tratamiento específico.',
        ]);
        AppointmentType::create([
            'name' => 'Examen Físico',
            'duration' => 30, // Duración en minutos
            // 'description' => 'Examen físico completo para evaluación de salud general.',
        ]);
        AppointmentType::create([
            'name' => 'Consulta de Urgencia',
            'duration' => 20, // Duración en minutos
            // 'description' => 'Atención médica urgente para problemas de salud que requieren atención inmediata.',
        ]);
        AppointmentType::create([
            'name' => 'Consulta de Seguimiento',
            'duration' => 30, // Duración en minutos
            // 'description' => 'Seguimiento de pacientes después de un tratamiento o procedimiento médico.',
        ]);
    }
}
