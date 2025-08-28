<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    /**
     * Campos que se pueden asignar en masa
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'estado',
        'prioridad',
        'creado_por',
        'asignado_a',
        'aprobado_por',
        'vence_en',
    ];

    /**
     * Relación con el creador del ticket
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Relación con la persona a la que se asigna el ticket
     */
    public function asignado()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    /**
     * Relación con el aprobador del ticket (Gerencia)
     */
    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    // al inicio del archivo, si quieres agregar import explícito (opcional)
    // use App\Models\TicketLog; // no es estrictamente necesario si queda en App\Models

    public function logs()
    {
        return $this->hasMany(TicketLog::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }


}
