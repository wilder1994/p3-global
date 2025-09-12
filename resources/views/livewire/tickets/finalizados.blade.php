<div class="p-6" wire:poll.10s>

    {{-- 🔍 Buscador --}}
    <div class="mb-4">
        <input type="text" wire:model.debounce.500ms="search" 
               class="border rounded px-3 py-2 w-full"
               placeholder="Buscar ticket por título...">
    </div>

    {{-- 📋 Tabla de tickets finalizados --}}
    <div class="bg-white shadow rounded-lg p-4 overflow-x-auto">
        <h3 class="font-semibold mb-4">Tickets Finalizados</h3>

        <table class="w-full border-collapse table-auto">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-2 border">Fecha</th>
                    <th class="p-2 border">Título</th>
                    <th class="p-2 border">Puesto</th>
                    <th class="p-2 border">Descripción</th>
                    <th class="p-2 border">Prioridad</th>
                    <th class="p-2 border">Responsable</th>
                    <th class="p-2 border">Creado por</th>
                    <th class="p-2 border text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $t)
                    @php
                        $rowColor = match(strtolower($t->prioridad)) {
                            'urgente' => 'bg-red-300',
                            'alta'    => 'bg-red-100',
                            'media'   => 'bg-yellow-100',
                            'baja'    => 'bg-green-100',
                            default   => ''
                        };
                    @endphp
                    <tr class="{{ $rowColor }} hover:border-2 hover:border-gray-500 transition-all">
                        <td class="p-2 border">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-2 border">{{ $t->titulo }}</td>
                        <td class="p-2 border">{{ $t->puesto }}</td>
                        <td class="p-2 border">{{ $t->descripcion }}</td>
                        <td class="p-2 border font-semibold">{{ ucfirst($t->prioridad) }}</td>
                        <td class="p-2 border">{{ $t->asignado?->name ?? 'Sin asignar' }}</td>
                        <td class="p-2 border">{{ $t->creador?->name ?? '---' }}</td>
                        <td class="p-2 border text-center">
                            <button wire:click="verDetalles({{ $t->id }})"
                                class="px-3 py-1 rounded bg-gray-600 text-white whitespace-nowrap hover:bg-gray-700 transition">
                                Detalles
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center p-4 text-gray-500">
                            No hay tickets finalizados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal de detalles --}}
    @if($mostrarModalDetalles && $ticketDetalle)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 
                        w-[95%] sm:w-[85%] md:w-[700px] lg:w-[900px] 
                        max-h-[90vh] overflow-y-auto mx-auto">
                <h2 class="text-lg font-bold mb-4">Detalles del Ticket #{{ $ticketDetalle->id }}</h2>

                <p><strong>Título:</strong> {{ $ticketDetalle->titulo }}</p>
                <p><strong>Descripción:</strong> {{ $ticketDetalle->descripcion }}</p>
                <p><strong>Prioridad:</strong> {{ ucfirst($ticketDetalle->prioridad) }}</p>
                <p><strong>Creado por:</strong> {{ $ticketDetalle->creador?->name ?? '—' }}</p>
                <p><strong>Responsable:</strong> {{ $ticketDetalle->asignado?->name ?? 'Sin asignar' }}</p>
                <p><strong>Fecha de creación:</strong> {{ $ticketDetalle->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($ticketDetalle->estado) }}</p>

                <h3 class="mt-4 font-semibold">Historial</h3>
                <ul class="list-disc pl-6 text-sm text-gray-700">
                    @foreach($ticketDetalle->logs as $log)
                        <li>
                            {{ $log->created_at->format('d/m/Y H:i') }} -
                            {{ $log->usuario?->name ?? 'Sistema' }} cambió de
                            <strong>{{ ucfirst($log->estado_anterior) }}</strong> a
                            <strong>{{ ucfirst($log->estado_nuevo) }}</strong>
                            @if($log->comentario)
                                ({{ $log->comentario }})
                            @endif
                        </li>
                    @endforeach
                </ul>

                <div class="flex justify-end mt-4">
                    <button wire:click="$set('mostrarModalDetalles', false)"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
