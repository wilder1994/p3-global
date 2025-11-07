<?php

namespace Tests\Feature\Livewire\Tickets;

use App\Livewire\Tickets\Board;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BoardInvalidTicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function confirmar_cambio_estado_muestra_mensaje_cuando_el_ticket_no_existe(): void
    {
        Livewire::test(Board::class)
            ->call('confirmarCambioEstado', 999, 'pendiente')
            ->assertSet('mostrarModal', false)
            ->assertSet('ticketSeleccionado', null)
            ->assertSessionHas('error', 'Ticket no encontrado.');
    }

    /** @test */
    public function finalizar_ticket_muestra_mensaje_cuando_el_ticket_no_existe(): void
    {
        Livewire::test(Board::class)
            ->call('finalizarTicket', 999)
            ->assertSessionHas('error', 'Ticket no encontrado.');
    }

    /** @test */
    public function aprobar_ticket_muestra_mensaje_cuando_el_ticket_no_existe(): void
    {
        Livewire::test(Board::class)
            ->call('aprobarTicket', 999)
            ->assertSessionHas('error', 'Ticket no encontrado.');
    }
}
