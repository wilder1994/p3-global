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

    // tickets será SIEMPRE una Collection de Collections de modelos (NO arrays)
    public $tickets;

    public $conteos = [];

    public $mostrarModal = false;
    public $ticketSeleccionado;
    public $nuevoEstado;
    public $comentario = '';
    public $responsable = null;
    public bool $cambioEstado = true;

    public $mostrarModalDetalles = false;
    public $ticketDetalle = null;

    /**
     * Estados que deben mostrarse en la tabla actual.
     */
    public array $estadosVisibles = [];

    /**
     * Texto del encabezado de la tabla.
     */
    public string $tituloTabla = 'Listado de Tickets';

    /**
     * Mensaje a mostrar cuando no existan tickets en la vista.
     */
    public string $mensajeVacio = 'No hay tickets registrados';

    protected $listeners = [
        'ticketCreado'              => 'loadTickets',
        'finalizarTicketDesdeModal' => 'finalizarTicketDesdeModal',
    ];

    protected ResponsibleUserService $responsibleUserService;

    public function boot(ResponsibleUserService $responsibleUserService): void
    {
        $this->responsibleUserService = $responsibleUserService;
    }

    public function mount(array $estadosVisibles = [], ?string $tituloTabla = null, ?string $mensajeVacio = null)
    {
        $this->estadosVisibles = $this->normalizarEstadosVisibles($estadosVisibles);

        if ($tituloTabla) {
            $this->tituloTabla = $tituloTabla;
        }

        if ($mensajeVacio) {
            $this->mensajeVacio = $mensajeVacio;
        }

        $this->loadTickets();
    }

    public function filtrar(): void
    {
        $this->loadTickets();
    }

    protected function normalizarEstadosVisibles(array $estadosVisibles): array
    {
        if (empty($estadosVisibles)) {
            return Ticket::ESTADOS_ACTIVOS;
        }

        $visibles = array_intersect($estadosVisibles, Ticket::ESTADOS_ACTIVOS);

        return array_values($visibles);
    }

    /**
     * Livewire hook: se ejecuta cada vez que cambia $search.
     * NO lo llames desde la vista. Sólo usa wire:model.
     */
    public function updatedSearch(): void
    {
        $this->loadTickets();
    }

    protected function loadTickets(): void
    {
        $ordenPrioridad = "FIELD(prioridad,'urgente','alta','media','baja')";

        $baseQuery = Ticket::with(['creador', 'asignado'])
            ->orderByRaw($ordenPrioridad)
            ->orderByDesc('created_at');

        // 🔍 Filtro por búsqueda
        if (!empty($this->search)) {
            $search = mb_strtolower(trim($this->search));

            $baseQuery->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(titulo) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(descripcion) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(prioridad) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(estado) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('creador', function ($q2) use ($search) {
                        $q2->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    })
                    ->orWhereHas('asignado', function ($q2) use ($search) {
                        $q2->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

        // 📊 Conteos por estado (evitando el error 1055)
        $conteos = (clone $baseQuery)
            ->reorder() // elimina ORDER BY heredados
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $this->conteos = [
            'pendiente'   => (int) ($conteos['pendiente']   ?? 0),
            'en_proceso'  => (int) ($conteos['en_proceso']  ?? 0),
            'finalizado'  => (int) ($conteos['finalizado']  ?? 0),
        ];

        // 🧾 Tickets activos por estado
        $activosQuery = (clone $baseQuery)->activos();

        // 👇 IMPORTANTE: NO usamos ->toArray(). Queremos modelos, no arrays.
        $this->tickets = collect(Ticket::ESTADOS_ACTIVOS)
            ->mapWithKeys(fn (string $estado) => [
                $estado => (clone $activosQuery)->estado($estado)->get(),
            ]);
    }

    public function confirmarCambioEstado($id, $estado, $cambioEstado = true)
    {
        $ticket = $this->findTicketOrNotify($id);

        if (! $ticket) {
            return;
        }

        $this->ticketSeleccionado = $id;
        $this->nuevoEstado        = $estado;
        $this->cambioEstado       = (bool) $cambioEstado;
        $this->comentario         = '';
        $this->responsable        = $ticket->asignado_a;
        $this->mostrarModal       = true;
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

        $estadoAnterior   = $ticket->estado;
        $asignadoAnterior = $ticket->asignado_a;

        $aplicarCambioEstado = $this->cambioEstado
            && $this->nuevoEstado
            && $this->nuevoEstado !== $estadoAnterior;

        if ($aplicarCambioEstado) {
            $ticket->estado = $this->nuevoEstado;
        }

        $comentarioFinal = $this->comentario;

        if ($this->responsable && $this->responsable != $asignadoAnterior) {
            $ticket->asignado_a = $this->responsable;
            $comentarioFinal    = '[Cambio de responsable] '.$comentarioFinal;
        }

        $ticket->save();

        TicketLog::create([
            'ticket_id'       => $ticket->id,
            'usuario_id'      => Auth::id(),
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'    => $ticket->estado,
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

    public function finalizarTicket($id, $comentario = null)
    {
        $ticket = $this->findTicketOrNotify($id);

        if (! $ticket) {
            return;
        }

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

    protected function findTicketOrNotify($id): ?Ticket
    {
        $ticket = Ticket::find($id);

        if (! $ticket) {
            session()->flash('error', 'Ticket no encontrado.');
            return null;
        }

        return $ticket;
    }

    public function verDetalles($id)
    {
        $this->ticketDetalle        = Ticket::with(['creador', 'logs.usuario'])->find($id);
        $this->mostrarModalDetalles = true;
    }

    protected function resetModal()
    {
        $this->mostrarModal       = false;
        $this->comentario         = '';
        $this->responsable        = null;
        $this->ticketSeleccionado = null;
        $this->nuevoEstado        = null;
        $this->cambioEstado       = true;
    }

    public function render()
    {
        $estados = $this->estadosVisibles ?: Ticket::ESTADOS_ACTIVOS;

        // $this->tickets es una Collection de [estado => Collection< Ticket >]
        $ticketsPorEstado = ($this->tickets instanceof \Illuminate\Support\Collection)
            ? $this->tickets->only($estados)
            : collect($this->tickets)->only($estados);

        $ticketsPlanos = $ticketsPorEstado
            ->flatMap(fn ($items) => $items) // sigue siendo Collection de modelos
            ->values();

        return view('livewire.tickets.board', [
            'usuarios'         => $this->responsibleUserService->all(),
            'ticketsPlanos'    => $ticketsPlanos,
            'ticketsPorEstado' => $ticketsPorEstado,
            'tituloTabla'      => $this->tituloTabla,
            'mensajeVacio'     => $this->mensajeVacio,
            'estadosVisibles'  => $this->estadosVisibles,
        ]);
    }
}
