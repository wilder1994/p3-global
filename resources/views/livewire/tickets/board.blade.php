<div class="p-6 flex flex-col h-screen">

    {{-- 🔍 Buscador --}}
    <div class="mb-4 flex space-x-2">
        <input type="text" wire:model.lazy="search" 
        placeholder="Buscar" 
            class="p-2 border rounded flex-1">
        <button class="p-2 bg-blue-500 text-white rounded">Buscar</button>
    </div>


    {{-- 📊 Tarjetas resumen por estado --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 flex-none">
        @foreach (['pendiente'=>'Pendiente','en_proceso'=>'En proceso','validacion'=>'Validación','finalizado'=>'Finalizados'] as $key => $titulo)
            <div class="bg-white rounded-xl p-3 shadow text-center">
                <h3 class="font-semibold mb-2">{{ $titulo }}</h3>
                <div class="text-3xl font-bold text-blue-600">
                    {{ $conteos[$key] ?? 0 }}
                </div>
            </div>
        @endforeach
    </div>


    {{-- 📋 Listado general de tickets con scroll --}}
    <div class="flex-1 bg-white shadow rounded-lg p-4 overflow-auto">
        <h3 class="font-semibold mb-4">Listado de Tickets</h3>

        <table class="w-full border-collapse table-auto">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border text-left">Fecha</th>
                    <th class="p-2 border text-left">Puesto</th>
                    <th class="p-2 border text-left">Descripción</th>
                    <th class="p-2 border text-left">Acciones</th>
                    <th class="p-2 border text-left">Prioridad</th>
                    <th class="p-2 border text-left">Creado</th>
                    <th class="p-2 border text-center">Detalles</th>
                    <th class="p-2 border text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $prioridades = [
                        'urgente' => 4,
                        'alta'    => 3,
                        'media'   => 2,
                        'baja'    => 1,
                    ];

                    $ticketsOrdenados = collect($tickets)
                        ->collapse() // en lugar de flatten(1)
                        ->sortByDesc(fn($ticket) => $prioridades[strtolower($ticket->prioridad)] ?? 0);
                @endphp

                @forelse ($ticketsOrdenados as $t)
                    @php
                        $rowColor = match(strtolower($t->prioridad)) {
                            'urgente' => 'bg-red-300',
                            'alta'    => 'bg-red-100',
                            'media'   => 'bg-yellow-100',
                            'baja'    => 'bg-green-100',
                            default   => ''
                        };

                        $flujos = [
                            'pendiente'  => 'en_proceso',
                            'en_proceso' => 'validacion',
                            'validacion' => 'finalizado',
                        ];
                        $siguiente = $flujos[$t->estado] ?? null;
                    @endphp

                    <tr class="{{ $rowColor }} hover:border-2 hover:border-gray-500 transition-all">
                        <td class="p-2 border">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-2 border">{{ $t->titulo }}</td>
                        <td class="p-2 border">{{ $t->descripcion }}</td>
                        <td class="p-2 border capitalize">{{ $t->estado }}</td>
                        <td class="p-2 border font-semibold">{{ ucfirst($t->prioridad) }}</td>
                        <td class="p-2 border">{{ $t->creador ? $t->creador->name : '---' }}</td>
                        <td class="p-2 border text-center">
                            <button wire:click="verDetalles({{ $t->id }})"
                                class="px-3 py-1 rounded bg-gray-600 text-white whitespace-nowrap">
                                Ver
                            </button>
                        </td>
                        <td class="p-2 border text-center">
                            @if ($siguiente)
                                <button wire:click="confirmarCambioEstado({{ $t->id }}, '{{ $siguiente }}')"
                                    class="px-3 py-1 rounded text-white whitespace-nowrap
                                        @if($siguiente==='en_proceso') bg-blue-500
                                        @elseif($siguiente==='validacion') bg-orange-500
                                        @elseif($siguiente==='finalizado') bg-green-600
                                        @endif">
                                    {{ ucfirst(str_replace('_',' ',$t->estado)) }}
                                </button>
                            @else
                                <span class="text-gray-400 italic">Finalizado</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center p-4 text-gray-500">
                            No hay tickets registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>


     {{-- Modal de confirmación de estado --}}
    @if($mostrarModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 
                        w-[90%] sm:w-[80%] md:w-[500px] lg:w-[600px] 
                        max-h-[90vh] overflow-y-auto mx-auto">
                <h2 class="text-lg font-bold mb-4">Confirmar cambio de estado</h2>
                <p class="mb-2 text-gray-700">
                    Este ticket pasará al estado:
                    <span class="font-semibold capitalize text-blue-600">{{ str_replace('_',' ',$nuevoEstado) }}</span>
                </p>
                <textarea wire:model="comentario"
                    placeholder="Escribe un comentario (opcional)"
                    class="w-full border rounded p-2 mb-4"></textarea>
                <div class="flex justify-end gap-2">
                    <button wire:click="$set('mostrarModal', false)"
                        class="bg-gray-400 text-white px-4 py-2 rounded">Cancelar</button>
                    <button wire:click="guardarCambioEstado"
                        class="bg-green-600 text-white px-4 py-2 rounded">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de detalles del ticket --}}
    @if($mostrarModalDetalles && $ticketDetalle)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 
                        w-[95%] sm:w-[85%] md:w-[700px] lg:w-[900px] 
                        max-h-[90vh] overflow-y-auto mx-auto">
                <h2 class="text-lg font-bold mb-4">Detalles del Ticket #{{ $ticketDetalle->id }}</h2>

                <p><strong>Título:</strong> {{ $ticketDetalle->titulo }}</p>
                <p><strong>Descripción:</strong> {{ $ticketDetalle->descripcion }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($ticketDetalle->estado) }}</p>
                <p><strong>Prioridad:</strong> {{ ucfirst($ticketDetalle->prioridad) }}</p>
                <p><strong>Creado por:</strong> {{ $ticketDetalle->creador?->name ?? '—' }}</p>
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

