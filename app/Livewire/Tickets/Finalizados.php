<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use App\Models\Ticket;

class Finalizados extends Component
{
    public $tickets;
    public $mostrarModalDetalles = false;
    public $ticketDetalle;
    public $search = '';

    public function mount()
    {
        $this->cargarTickets();
    }

    public function updatedSearch()
    {
        $this->cargarTickets();
    }

    public function cargarTickets()
    {
        $query = Ticket::with(['creador', 'asignado', 'logs.usuario'])
            ->where('estado', 'finalizado')
            ->orderByRaw("FIELD(prioridad,'urgente','alta','media','baja')")
            ->orderByDesc('created_at');

        if (!empty($this->search)) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%$search%")
                  ->orWhere('descripcion', 'like', "%$search%")
                  ->orWhereHas('creador', fn ($q2) => $q2->where('name', 'like', "%$search%"));
            });
        }

        $this->tickets = $query->get();
    }

    public function verDetalles($id)
    {
        $this->ticketDetalle = Ticket::with(['creador', 'logs.usuario'])->find($id);
        $this->mostrarModalDetalles = true;
    }

    public function render()
    {
        return view('livewire.tickets.finalizados');
    }
}
