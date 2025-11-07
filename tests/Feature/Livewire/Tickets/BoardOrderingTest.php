<?php

namespace Tests\Feature\Livewire\Tickets;

use App\Livewire\Tickets\Board;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BoardOrderingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function los_tickets_activos_se_ordenan_por_prioridad_y_fecha(): void
    {
        config(['tickets.responsable_roles' => []]);

        $creador = User::factory()->create();

        Carbon::setTestNow('2024-01-01 09:50:00');
        $pendienteBaja = $this->createTicket($creador, [
            'titulo' => 'Pendiente baja',
            'prioridad' => 'baja',
        ]);

        Carbon::setTestNow('2024-01-01 09:55:00');
        $pendienteAltaAntigua = $this->createTicket($creador, [
            'titulo' => 'Pendiente alta antigua',
            'prioridad' => 'alta',
        ]);

        Carbon::setTestNow('2024-01-01 10:05:00');
        $pendienteAltaReciente = $this->createTicket($creador, [
            'titulo' => 'Pendiente alta reciente',
            'prioridad' => 'alta',
        ]);

        Carbon::setTestNow('2024-01-01 10:10:00');
        $pendienteUrgente = $this->createTicket($creador, [
            'titulo' => 'Pendiente urgente',
            'prioridad' => 'urgente',
        ]);

        Carbon::setTestNow('2024-01-01 09:40:00');
        $enProcesoMedia = $this->createTicket($creador, [
            'titulo' => 'En proceso media',
            'estado' => 'en_proceso',
            'prioridad' => 'media',
        ]);

        Carbon::setTestNow('2024-01-01 09:45:00');
        $enProcesoUrgente = $this->createTicket($creador, [
            'titulo' => 'En proceso urgente',
            'estado' => 'en_proceso',
            'prioridad' => 'urgente',
        ]);

        Carbon::setTestNow();

        $component = Livewire::test(Board::class);

        $ticketsPendientes = collect($component->get('tickets')['pendiente'])->pluck('id')->all();
        $this->assertSame(
            [
                $pendienteUrgente->id,
                $pendienteAltaReciente->id,
                $pendienteAltaAntigua->id,
                $pendienteBaja->id,
            ],
            $ticketsPendientes
        );

        $ticketsEnProceso = collect($component->get('tickets')['en_proceso'])->pluck('id')->all();
        $this->assertSame(
            [
                $enProcesoUrgente->id,
                $enProcesoMedia->id,
            ],
            $ticketsEnProceso
        );
    }

    private function createTicket(User $creador, array $overrides = []): Ticket
    {
        $defaults = [
            'titulo' => 'Ticket de prueba',
            'puesto' => 'Puesto',
            'cargo' => 'Cargo',
            'nombre_guarda' => 'Nombre',
            'cedula_guarda' => '1234567890',
            'descripcion' => 'Descripción',
            'estado' => 'pendiente',
            'prioridad' => 'media',
            'creado_por' => $creador->id,
            'asignado_a' => null,
            'aprobado_por' => null,
            'vence_en' => null,
        ];

        return Ticket::create(array_merge($defaults, $overrides));
    }
}
