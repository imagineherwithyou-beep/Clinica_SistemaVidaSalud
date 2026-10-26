<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Role extends Model
{
  // Nombres tal como se guardan en roles.nombre_rol
    public const ADMINISTRADOR = 'Administrador';
    public const RECEPCIONISTA = 'Recepcionista';
    public const MEDICO = 'Medico';
    public const PACIENTE = 'Paciente';

    protected $table = 'roles';

    protected $fillable = ['nombre_rol'];

    // Nombre para mostrar en pantalla (en la BD "Medico" va sin tilde)
    public function getEtiquetaAttribute(): string
    {
        return $this->nombre_rol === self::MEDICO ? 'Médico' : $this->nombre_rol;
    }

    // Clase CSS con el color del rol: rol-administrador, rol-medico, ...
    public function getClaseAttribute(): string
    {
        return 'rol-'.Str::slug($this->nombre_rol);
    }

    /** Opciones [id => etiqueta] para <x-ui.select>. */
    public static function opciones(): Collection
    {
        return static::orderBy('nombre_rol')->get()
            ->mapWithKeys(fn (self $rol) => [$rol->id => $rol->etiqueta]);
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}
