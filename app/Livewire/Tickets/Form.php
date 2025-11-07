<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use App\Models\Ticket;
use App\Services\ResponsibleUserService;
use Illuminate\Validation\Rule;

class Form extends Component
{
    public $puesto;
    public $asunto;
    public $cargo;
    public $nombre;
    public $cedula;
    public $responsable;
    public $descripcion;
    public $prioridad = 'media';

    protected ResponsibleUserService $responsibleUserService;

    public function boot(ResponsibleUserService $responsibleUserService): void
    {
        $this->responsibleUserService = $responsibleUserService;
    }

    public function rules()
    {
        return [
            'puesto'      => 'required|string|min:3',
            'asunto'      => 'required|string|min:3',
            'cargo'       => 'required|string|min:2',
            'nombre'      => 'required|string|min:3',
            'cedula'      => 'required|regex:/^\d{6,20}$/',
            'responsable' => 'required|exists:users,id',
            'descripcion' => 'required|string|min:3',
            'prioridad'   => ['required', Rule::in(['urgente','alta','media','baja'])],
        ];
    }

    public function crear()
    {
        $this->validate();

        Ticket::create([
            'titulo'        => $this->asunto,
            'puesto'        => $this->puesto,
            'cargo'         => $this->cargo,
            'nombre_guarda' => $this->nombre,
            'cedula_guarda' => $this->cedula,
            'descripcion'   => $this->descripcion,
            'prioridad'     => $this->prioridad,
            'estado'        => 'pendiente',
            'creado_por'    => auth()->id(),
            'asignado_a'    => $this->responsable,
        ]);

        $this->reset(['puesto','asunto','cargo','nombre','cedula','responsable','descripcion']);
        $this->prioridad = 'media';

        $this->dispatch('ticketCreado');
        $this->dispatch('notify', msg: 'Ticket creado correctamente');
    }

    public function render()
    {
        return view('livewire.tickets.form', [
            'usuarios' => $this->responsibleUserService->all(),
        ]);
    }
}