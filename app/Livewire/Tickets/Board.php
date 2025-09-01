<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\TicketLog;
use Illuminate\Support\Facades\Auth;

class Board extends Component
{
    public $search = '';

    // 👉 Tickets activos para la tabla
    public $tickets = [
        'pendiente'  => [],
        'en_proceso' => [],
        'validacion' => [],
    ];

    // 👉 Conteos para las tarjetas
    public $conteos = [
        'pendiente'  => 0,
        'en_proceso' => 0,
        'validacion' => 0,
        'finalizado' => 0,
    ];

    public $mostrarModal = false;   // controla el modal
    public $ticketSeleccionado;     // ticket en edición
    public $nuevoEstado;            // estado al que se moverá
    public $comentario = '';        // comentario escrito por el usuario

    protected $listeners = ['ticketCreado' => 'loadTickets'];

    public function mount()
    {
        $this->loadTickets();
    }

    // abrir modal antes de confirmar
    public function confirmarCambioEstado($id, $estado)
    {
        $this->ticketSeleccionado = $id;
        $this->nuevoEstado = $estado;
        $this->comentario = '';
        $this->mostrarModal = true;
    }

    // guardar cambio con log
    public function guardarCambioEstado()
    {
        $ticket = Ticket::find($this->ticketSeleccionado);

        if (! $ticket) {
            session()->flash('error', 'Ticket no encontrado.');
            return;
        }

        $estadoAnterior = $ticket->estado;

        $ticket->estado = $this->nuevoEstado;
        $ticket->save();

        TicketLog::create([
            'ticket_id'       => $ticket->id,
            'usuario_id'      => Auth::id(),
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => $this->nuevoEstado,
            'comentario'      => $this->comentario,
        ]);

        $this->mostrarModal = false;
        $this->ticketSeleccionado = null;
        $this->comentario = '';

        $this->loadTickets();

        session()->flash('success', 'Estado actualizado y log registrado.');
    }

    protected function loadTickets()
    {
        $ordenPrioridad = "FIELD(prioridad,'urgente','alta','media','baja')";

        // Traemos todos los tickets (para conteos)
        $todos = Ticket::with('creador')
            ->orderByRaw($ordenPrioridad)
            ->latest();

        if ($this->search) {
            $search = $this->search;
            $todos->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%")
                    ->orWhere('prioridad', 'like', "%{$search}%")
                    ->orWhere('estado', 'like', "%{$search}%")
                    ->orWhereHas('creador', function($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $todos = $todos->get();

        // 👉 Conteos para tarjetas
        $this->conteos = [
            'pendiente'  => $todos->where('estado', 'pendiente')->count(),
            'en_proceso' => $todos->where('estado', 'en_proceso')->count(),
            'validacion' => $todos->where('estado', 'validacion')->count(),
            'finalizado' => $todos->where('estado', 'finalizado')->count(),
        ];

        // 👉 Solo tickets activos para la tabla
        $activos = $todos->filter(fn($t) => strtolower($t->estado) !== 'finalizado');

        $this->tickets = [
            'pendiente'  => $activos->filter(fn($t) => strtolower($t->estado) === 'pendiente'),
            'en_proceso' => $activos->filter(fn($t) => strtolower($t->estado) === 'en_proceso'),
            'validacion' => $activos->filter(fn($t) => strtolower($t->estado) === 'validacion'),
        ];
    }

    public function updatedSearch()
    {
        $this->loadTickets();
    }

    public function render()
    {
        return view('livewire.tickets.board');
    }

    public $mostrarModalDetalles = false;
    public $ticketDetalle = null;

    // Abrir modal con detalles
    public function verDetalles($id)
    {
        $this->ticketDetalle = Ticket::with(['creador', 'logs.usuario'])->find($id);
        $this->mostrarModalDetalles = true;
    }

}
