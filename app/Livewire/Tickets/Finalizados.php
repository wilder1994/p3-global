<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use App\Models\Ticket;

/**
 * @method $this layout(string $view)
 */
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

    public function cargarTickets()
    {
        $this->tickets = Ticket::where('estado', 'finalizado')
            ->orderByDesc('updated_at')
            ->get();
    }

    public function verDetalles($id)
    {
        $this->ticketDetalle = Ticket::find($id);
        $this->mostrarModalDetalles = true;
    }

    public function render()
    {
        return view('livewire.tickets.finalizados')->layout('layouts.app');
    }
}
