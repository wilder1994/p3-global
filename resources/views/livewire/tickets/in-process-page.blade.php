<x-app-layout>
    <div class="w-full p-6">
        @livewire('tickets.board', [
            'estadosVisibles' => ['en_proceso'],
            'tituloTabla' => 'Tickets en proceso',
            'mensajeVacio' => 'No hay tickets en proceso en este momento.',
        ])
    </div>
</x-app-layout>
