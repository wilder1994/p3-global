<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    /**
     * Catálogo de estados permitidos para los tickets.
     *
     * - pendiente   → Ticket recién creado que espera ser atendido.
     * - en_proceso  → Ticket en ejecución por parte del responsable.
     * - validacion  → Ticket completado que requiere aprobación.
     * - finalizado  → Ticket concluido y aprobado.
     * - rechazado   → Ticket invalidado durante la revisión.
     * - cerrado     → Ticket clausurado manualmente por un administrador.
     */
    public const ESTADOS = [
        'pendiente',
        'en_proceso',
        'validacion',
        'finalizado',
        'rechazado',
        'cerrado',
    ];

    /**
     * Subconjunto de estados considerados "activos" para tableros y vistas.
     */
    public const ESTADOS_ACTIVOS = [
        'pendiente',
        'en_proceso',
        'validacion',
    ];

    /**
     * Estados finales que se utilizan para métricas y filtros posteriores.
     */
    public const ESTADOS_FINALIZADOS = [
        'finalizado',
        'rechazado',
        'cerrado',
    ];

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
        return $query->whereIn('estado', self::ESTADOS_ACTIVOS);
    }
}
