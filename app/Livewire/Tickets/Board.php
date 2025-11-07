<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Services\ResponsibleUserService;
use Illuminate\Support\Facades\Auth;

class Board extends Component
{
    public $search = '';

    public $tickets = [
        'pendiente'  => [],
        'en_proceso' => [],
        'validacion' => [],
    ];

    public $conteos = [
        'pendiente'  => 0,
        'en_proceso' => 0,
        'validacion' => 0,
        'finalizado' => 0,
        'rechazado'  => 0,
        'cerrado'    => 0,
    ];

    public $mostrarModal = false;
    public $ticketSeleccionado;
    public $nuevoEstado;
    public $comentario = '';
    public $responsable = null;

    public $mostrarModalDetalles = false;
    public $ticketDetalle = null;

    protected $listeners = [
        'ticketCreado' => 'loadTickets',
        'finalizarTicketDesdeModal' => 'finalizarTicketDesdeModal',
        'aprobarTicketDesdeModal' => 'aprobarTicketDesdeModal',
    ];

    protected ResponsibleUserService $responsibleUserService;

    public function boot(ResponsibleUserService $responsibleUserService): void
    {
        $this->responsibleUserService = $responsibleUserService;
    }

    public function mount()
    {
        $this->loadTickets();
    }

    public function confirmarCambioEstado($id, $estado)
    {
        $ticket = $this->findTicketOrNotify($id);

        if (! $ticket) {
            return;
        }

        $this->ticketSeleccionado = $id;
        $this->nuevoEstado = $estado;
        $this->comentario = '';
        $this->responsable = $ticket->asignado_a;
        $this->mostrarModal = true;
    }

    public function guardarCambioEstado()
    {
        $this->validate([
            'comentario' => 'required|string|min:3',
        ], [
            'comentario.required' => 'Debe escribir un comentario para continuar.',
        ]);

        $ticket = Ticket::find($this->ticketSeleccionado);

        if (! $ticket) {
            session()->flash('error', 'Ticket no encontrado.');
            return;
        }

        $estadoAnterior = $ticket->estado;
        $asignadoAnterior = $ticket->asignado_a;
        $ticket->estado = $this->nuevoEstado;

        $comentarioFinal = $this->comentario;

        if ($this->responsable && $this->responsable != $asignadoAnterior) {
            $ticket->asignado_a = $this->responsable;
            $comentarioFinal = "[Cambio de responsable] " . $comentarioFinal;
        }

        $ticket->save();

        TicketLog::create([
            'ticket_id'       => $ticket->id,
            'usuario_id'      => Auth::id(),
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => $this->nuevoEstado,
            'comentario'      => $comentarioFinal,
        ]);

        $this->resetModal();
        $this->loadTickets();

        session()->flash('success', 'Estado actualizado correctamente.');
    }

    public function finalizarTicketDesdeModal()
    {
        $this->validate([
            'comentario' => 'required|string|min:3',
        ], [
            'comentario.required' => 'Debe escribir un comentario para finalizar el ticket.',
        ]);

        $this->finalizarTicket($this->ticketSeleccionado, $this->comentario);
        $this->resetModal();
    }

    public function aprobarTicketDesdeModal()
    {
        $this->validate([
            'comentario' => 'required|string|min:3',
        ], [
            'comentario.required' => 'Debe escribir un comentario para aprobar el ticket.',
        ]);

        $this->aprobarTicket($this->ticketSeleccionado, $this->comentario);
        $this->resetModal();
    }

    public function finalizarTicket($id, $comentario = null)
    {
        $ticket = $this->findTicketOrNotify($id);

        if (! $ticket) return;

        $estadoAnterior = $ticket->estado;
        $ticket->estado = 'finalizado';
        $ticket->save();

        TicketLog::create([
            'ticket_id'       => $ticket->id,
            'usuario_id'      => Auth::id(),
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => 'finalizado',
            'comentario'      => $comentario ?? 'Finalizado directamente',
        ]);

        $this->loadTickets();
    }

    public function aprobarTicket($id, $comentario = null)
    {
        $ticket = $this->findTicketOrNotify($id);

        if (! $ticket) return;

        $estadoAnterior = $ticket->estado;
        $ticket->estado = 'validacion';
        $ticket->aprobado_por = Auth::id();
        $ticket->save();

        TicketLog::create([
            'ticket_id'       => $ticket->id,
            'usuario_id'      => Auth::id(),
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => 'validacion',
            'comentario'      => $comentario ?? 'Aprobado directamente',
        ]);

        $this->loadTickets();
    }

    protected function findTicketOrNotify($id): ?Ticket
    {
        $ticket = Ticket::find($id);

        if (! $ticket) {
            session()->flash('error', 'Ticket no encontrado.');

            return null;
        }

        return $ticket;
    }

    protected function loadTickets()
    {
        $ordenPrioridad = "FIELD(prioridad,'urgente','alta','media','baja')";

        $todos = Ticket::with(['creador', 'asignado'])
            ->orderByRaw($ordenPrioridad)
            ->orderByDesc('created_at');

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

        $this->conteos = [
            'pendiente'  => $todos->where('estado', 'pendiente')->count(),
            'en_proceso' => $todos->where('estado', 'en_proceso')->count(),
            'validacion' => $todos->where('estado', 'validacion')->count(),
            'finalizado' => $todos->where('estado', 'finalizado')->count(),
            'rechazado'  => $todos->where('estado', 'rechazado')->count(),
            'cerrado'    => $todos->where('estado', 'cerrado')->count(),
        ];

        $activos = $todos->filter(fn($t) => !in_array(strtolower($t->estado), ['finalizado','rechazado','cerrado']));

        $this->tickets = [
            'pendiente'  => $activos->where('estado', 'pendiente'),
            'en_proceso' => $activos->where('estado', 'en_proceso'),
            'validacion' => $activos->where('estado', 'validacion'),
        ];
    }

    public function updatedSearch()
    {
        $this->loadTickets();
    }

    public function render()
    {
        return view('livewire.tickets.board', [
            'usuarios' => $this->responsibleUserService->all(),
        ]);
    }

    public function verDetalles($id)
    {
        $this->ticketDetalle = Ticket::with(['creador', 'logs.usuario'])->find($id);
        $this->mostrarModalDetalles = true;
    }

    protected function resetModal()
    {
        $this->mostrarModal = false;
        $this->comentario = '';
        $this->responsable = null;
        $this->ticketSeleccionado = null;
    }
}
