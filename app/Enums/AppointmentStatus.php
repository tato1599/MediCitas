<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case REALIZADO = 'RE'; // Completed
    case PROGRAMADO = 'PR'; // Scheduled
    case AUSENTE = 'AU'; // No-show
    case CANCELADO = 'CA'; // Canceled
    case CONFIRMADO = 'CO'; // Confirmed
    case REPROGRAMADO = 'RP'; // Rescheduled

    public static function getColors(): array
    {
        $colors = [
            'RE' => '#4CAF50', // Realizado - Verde
            'PR' => '#2196F3', // Programado - Azul
            'AU' => '#FF5722', // Ausente - Naranja
            'CA' => '#F44336', // Cancelado - Rojo
            'CO' => '#009688', // Confirmado - Verde Azulado
            'RP' => '#FFC107', // Reprogramado - Amarillo
        ];

        return $colors;
    }

    public static function toLiveWireArray($idKey = 'id', $valueKey = 'name', bool $usingValueAsName = true): array
    {
        $types = [];
        foreach (self::cases() as $type) {
            $types[] = [
                $idKey => $type->value,
                $valueKey => ucwords(strtolower($usingValueAsName ? $type->value : $type->name)),
            ];
        }

        return $types;
    }

    public static function fromValue(string $value): static
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        throw new \InvalidArgumentException("Invalid value: $value");
    }
}
