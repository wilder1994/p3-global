<x-app-layout>
    <div class="w-full p-6">
        {{--👈 SOLO pendientes--}}
        @livewire('tickets.board', [
            'estadosVisibles' => ['pendiente'],  
            'tituloTabla' => 'Tickets pendientes',
            'mensajeVacio' => 'No hay tickets pendientes en este momento.',
        ])
    </div>
</x-app-layout>
