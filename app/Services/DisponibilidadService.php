<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\HorarioAtencion;
use Carbon\CarbonImmutable;

class DisponibilidadService
{
    /**
     * Genera los intervalos del horario de un médico y marca los que están ocupados.
     * La reserva debe volver a comprobarse en el servidor al implementar CU14.
     *
     * @return array<int, array{inicio: string, fin: string, disponible: bool}>
     */
    public function intervalos(int $medicoId, string $fecha): array
    {
        $date = CarbonImmutable::parse($fecha);
        $day = $date->dayOfWeekIso;
        $horarios = HorarioAtencion::where('medico_id', $medicoId)->where('dia_semana', $day)
            ->orderBy('hora_inicio')->get();

        if ($horarios->isEmpty()) {
            return [];
        }

        $citas = Cita::where('medico_id', $medicoId)->whereDate('fecha_cita', $fecha)
            ->whereIn('estado', ['Programada', 'Confirmada'])
            ->orderBy('hora_inicio')->get(['hora_inicio', 'hora_fin']);

        $now = CarbonImmutable::now();
        $slots = [];
        foreach ($horarios as $horario) {
            if ($horario->intervalo_minutos < 1) {
                continue;
            }
            $blockStart = CarbonImmutable::parse($fecha.' '.$horario->hora_inicio);
            $blockEnd = CarbonImmutable::parse($fecha.' '.$horario->hora_fin);
            for ($start = $blockStart; ; $start = $start->addMinutes($horario->intervalo_minutos)) {
                $end = $start->addMinutes($horario->intervalo_minutos);
                if ($end->greaterThan($blockEnd)) {
                    break;
                }
                if ($start->lessThanOrEqualTo($now)) {
                    continue;
                }

                $occupied = $citas->contains(function (Cita $cita) use ($fecha, $start, $end) {
                    $appointmentStart = CarbonImmutable::parse($fecha.' '.$cita->hora_inicio);
                    $appointmentEnd = CarbonImmutable::parse($fecha.' '.$cita->hora_fin);
                    return $start->lessThan($appointmentEnd) && $end->greaterThan($appointmentStart);
                });

                $slots[] = [
                    'inicio' => $start->format('H:i'),
                    'fin' => $end->format('H:i'),
                    'disponible' => ! $occupied,
                ];
            }
        }

        return $slots;
    }
}
