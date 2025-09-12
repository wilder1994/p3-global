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
}
