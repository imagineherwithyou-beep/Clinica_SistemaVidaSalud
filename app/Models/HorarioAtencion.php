<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioAtencion extends Model
{
    protected $table = 'horarios_atencion';

    protected $fillable = ['medico_id', 'dia_semana', 'hora_inicio', 'hora_fin', 'intervalo_minutos'];

    protected $casts = ['dia_semana' => 'integer', 'intervalo_minutos' => 'integer'];

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }
}
