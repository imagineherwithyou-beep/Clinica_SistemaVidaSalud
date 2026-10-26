<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    protected $fillable = ['nombre_usuario', 'email', 'password', 'rol_id', 'estado'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // se encripta (bcrypt) al asignarla
        'estado' => 'boolean',
    ];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function medico(): HasOne
    {
        return $this->hasOne(Medico::class, 'user_id');
    }

    public function tieneRol(string ...$roles): bool
    {
        return in_array($this->rol?->nombre_rol, $roles, true);
    }

    /** Módulos que ve este usuario según su rol (config/menu.php). */
    public function modulos(): Collection
    {
        $rol = $this->rol?->nombre_rol;

        return collect($rol ? config("menu.{$rol}", []) : []);
    }

    /** Inicial para el avatar. */
    public function inicial(): string
    {
        return mb_strtoupper(mb_substr($this->nombre_usuario, 0, 1));
    }
}
