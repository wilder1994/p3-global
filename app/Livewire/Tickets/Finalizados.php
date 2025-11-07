<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use App\Models\Ticket;

class Finalizados extends Component
{
    public $tickets = [];
    public $search = '';

    public $mostrarModalDetalles = false;
    public $ticketDetalle = null;

    // Para usar las mismas tarjetas que el dashboard / board
    public $conteos = [];

    protected $listeners = [
        'ticketCreado' => 'loadTickets',
    ];

    public function mount()
    {
        $this->loadTickets();
    }

    /**
     * Método que llama el input:
     * wire:input.debounce.500ms="filtrar"
     */
    public function filtrar(): void
    {
        $this->loadTickets();
    }

    protected function loadTickets(): void
    {
        $query = Ticket::with(['creador', 'asignado'])
            ->where('estado', 'finalizado')
            ->orderByDesc('created_at');

        if (!empty($this->search)) {
            $search = mb_strtolower(trim($this->search));

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(titulo) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(descripcion) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(puesto) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('asignado', function ($q2) use ($search) {
                        $q2->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    })
                    ->orWhereHas('creador', function ($q2) use ($search) {
                        $q2->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

        $this->tickets = $query->get();

        // Conteos para las tarjetas (reutilizamos el modelo)
        $this->conteos = [
            'pendiente'  => Ticket::where('estado', 'pendiente')->count(),
            'en_proceso' => Ticket::where('estado', 'en_proceso')->count(),
            'finalizado' => Ticket::where('estado', 'finalizado')->count(),
        ];
    }

    public function verDetalles($id): void
    {
        $this->ticketDetalle = Ticket::with(['creador', 'asignado', 'logs.usuario'])->find($id);
        $this->mostrarModalDetalles = true;
    }

    public function render()
    {
        return view('livewire.tickets.finalizados', [
            'tickets' => $this->tickets,
            'conteos' => $this->conteos,
        ]);
    }
}
