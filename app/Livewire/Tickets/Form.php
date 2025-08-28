<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use App\Models\Ticket;

class Form extends Component
{
    public $puesto, $tipo_novedad, $descripcion, $prioridad = 'media';

    protected $rules = [
        'puesto'       => 'required|min:3',
        'descripcion'  => 'required|string|min:3',
        'prioridad'    => 'required|in:urgente,alta,media,baja',
    ];

    public function crear()
    {
        $this->validate();

        Ticket::create([
            'titulo'       => $this->puesto,         // ⚠️ aquí lo guardamos como 'titulo' en la DB
            'descripcion'  => $this->descripcion,
            'prioridad'    => $this->prioridad,
            'estado'       => 'pendiente',           // por defecto
            'creado_por'   => auth()->id(),
        ]);

        // Reseteamos el formulario
        $this->reset(['puesto','descripcion','prioridad']);
        $this->prioridad = 'media';

        // Notificación al frontend
        $this->dispatch('ticketCreado');
        $this->dispatch('notify', msg: 'Ticket creado');
    }

    public function render()
    {
        return view('livewire.tickets.form');
    }
}
