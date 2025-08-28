<div class="p-6" wire:poll.5s>
    {{-- 🔍 Buscador --}}
    <div class="mb-4">
        <input type="text" wire:model.debounce.500ms="search" 
               class="border rounded px-3 py-2 w-full"
               placeholder="Buscar ticket por título...">
    </div>

    <div class="mt-8 bg-white shadow rounded-lg p-4 overflow-x-auto">
        <h3 class="font-semibold mb-4">Tickets Finalizados</h3>

        <table class="w-full border-collapse table-auto">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Título</th>
                    <th class="p-2 border">Descripción</th>
                    <th class="p-2 border">Prioridad</th>
                    <th class="p-2 border">Creado por</th>
                    <th class="p-2 border text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $t)
                    <tr class="hover:bg-gray-50 bg-green-100">
                        <td class="p-2 border">#{{ $t->id }}</td>
                        <td class="p-2 border">{{ $t->titulo }}</td>
                        <td class="p-2 border">{{ $t->descripcion }}</td>
                        <td class="p-2 border font-semibold">{{ ucfirst($t->prioridad) }}</td>
                        <td class="p-2 border">{{ $t->creador?->name ?? '---' }}</td>
                        <td class="p-2 border text-center">
                            <button wire:click="verDetalles({{ $t->id }})"
                                class="px-3 py-1 rounded bg-gray-600 text-white whitespace-nowrap">
                                Detalles
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center p-4 text-gray-500">
                            No hay tickets finalizados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal de detalles --}}
    @if($mostrarModalDetalles && $ticketDetalle)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-1/2">
                <h2 class="text-lg font-bold mb-4">Detalles del Ticket #{{ $ticketDetalle->id }}</h2>

                <p><strong>Título:</strong> {{ $ticketDetalle->titulo }}</p>
                <p><strong>Descripción:</strong> {{ $ticketDetalle->descripcion }}</p>
                <p><strong>Prioridad:</strong> {{ ucfirst($ticketDetalle->prioridad) }}</p>
                <p><strong>Creado por:</strong> {{ $ticketDetalle->usuario?->name ?? '—' }}</p>
                <p><strong>Fecha de creación:</strong> {{ $ticketDetalle->created_at->format('d/m/Y H:i') }}</p>

                <h3 class="mt-4 font-semibold">Historial</h3>
                <ul class="list-disc pl-6">
                    @foreach($ticketDetalle->logs as $log)
                        <li>
                            {{ $log->created_at->format('d/m/Y H:i') }} - 
                            {{ $log->usuario?->name ?? 'Sistema' }} cambió de 
                            <strong>{{ $log->estado_anterior }}</strong> a 
                            <strong>{{ $log->estado_nuevo }}</strong>
                            @if($log->comentario)
                                ({{ $log->comentario }})
                            @endif
                        </li>
                    @endforeach
                </ul>

                <div class="flex justify-end mt-4">
                    <button wire:click="$set('mostrarModalDetalles', false)"
                        class="bg-gray-400 text-white px-4 py-2 rounded">Cerrar</button>
                </div>
            </div>
        </div>
    @endif
</div>
