<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'puesto',
        'cargo',
        'nombre_guarda',
        'cedula_guarda',
        'descripcion',
        'estado',
        'prioridad',
        'creado_por',
        'asignado_a',
        'aprobado_por',
        'vence_en',
    ];

    protected $casts = [
        'vence_en' => 'datetime',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function asignado()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function logs()
    {
        return $this->hasMany(TicketLog::class);
    }

    /**
     * Scope a query to only include tickets that match the given estado.
     */
    public function scopeEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope a query to only include active tickets that are not finalizados, rechazados o cerrados.
     */
    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['pendiente', 'en_proceso', 'validacion']);
    }
}
