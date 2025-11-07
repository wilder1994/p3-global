
    <div class="p-6 flex flex-col h-screen w-full">

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
                        <th class="p-2 border text-left">Creado</th>
                        <th class="p-2 border text-left">Asunto</th>
                        <th class="p-2 border text-left">Puesto</th>
                        {{--<th class="p-2 border text-left">Cargo</th>--}}
                        {{--<th class="p-2 border text-left">Nombre guarda</th>--}}
                        {{--<th class="p-2 border text-left">Cédula</th>--}}
                        <th class="p-2 border text-left">Descripción</th>
                        <th class="p-2 border text-left">Asignado a</th>
                        <th class="p-2 border text-left">Prioridad</th>
                        <th class="p-2 border text-center">Detalles</th>
                        <th class="p-2 border text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (collect($tickets)->collapse() as $t)
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
                            <td class="p-2 border">{{ $t->creador?->name ?? '---' }}</td>
                            <td class="p-2 border">{{ $t->titulo }}</td> {{-- Asunto --}}
                            <td class="p-2 border">{{ $t->puesto }}</td>
                            {{--<td class="p-2 border">{{ $t->cargo }}</td>--}}
                            {{--<td class="p-2 border">{{ $t->nombre_guarda }}</td>--}}
                            {{--<td class="p-2 border">{{ $t->cedula_guarda }}</td>--}}
                            <td class="p-2 border">{{ $t->descripcion }}</td>
                            <td class="p-2 border">{{ $t->asignado?->name ?? 'Sin asignar' }}</td>
                            <td class="p-2 border font-semibold">{{ ucfirst($t->prioridad) }}</td>
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
                            <td colspan="11" class="text-center p-4 text-gray-500">
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
                        <span class="font-semibold capitalize text-blue-600">
                            {{ str_replace('_',' ', $nuevoEstado) }}
                        </span>
                    </p>

                    {{-- Comentario --}}
                    <textarea wire:model="comentario"
                        placeholder="Debe escribir un comentario obligatorio"
                        class="w-full border rounded p-2 mb-1"></textarea>

                    @error('comentario')
                        <div class="text-red-600 text-sm mb-3">{{ $message }}</div>
                    @enderror


                    {{-- Select de responsable --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Asignar responsable:</label>
                        <select wire:model.defer="responsable" class="border rounded px-3 py-2 w-full bg-white">
                            @if($usuarios->isEmpty())
                                <option value="" disabled>No hay responsables disponibles</option>
                            @else
                                <option value="">Seleccionar responsable</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('responsable')
                            <div class="text-red-600 text-sm mt-1">Debe asignar un responsable</div>
                        @enderror
                    </div>

                    {{-- Botones --}}
                    <div class="flex flex-wrap justify-end gap-2 mt-4">
                        {{-- Cancelar --}}
                        <button wire:click="$set('mostrarModal', false)"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                            Cancelar
                        </button>

                        {{-- Confirmar cambio de estado normal --}}
                        <button wire:click="guardarCambioEstado"
                            class="bg-blue-600 text-white px-4 py-2 rounded">
                            Confirmar
                        </button>

                        {{-- Aprobar directamente --}}
                        {{-- <button wire:click="aprobarTicketDesdeModal"
                            class="bg-green-600 text-white px-4 py-2 rounded">
                            Aprobar
                        </button> --}}

                        {{-- Finalizar directamente --}}
                        <button wire:click="finalizarTicketDesdeModal"
                            class="bg-gray-600 text-white px-4 py-2 rounded">
                            Finalizar
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

                    <p><strong>Fecha de creación:</strong> {{ $ticketDetalle->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Creado por:</strong> {{ $ticketDetalle->creador?->name ?? '—' }}</p>
                    <p><strong>Asunto:</strong> {{ $ticketDetalle->titulo }}</p>
                    <p><strong>Puesto:</strong> {{ $ticketDetalle->puesto }}</p>
                    <p><strong>Nombre del guarda:</strong> {{ $ticketDetalle->nombre_guarda }}</p>
                    <p><strong>Cédula del guarda:</strong> {{ $ticketDetalle->cedula_guarda }}</p>
                    <p><strong>Cargo:</strong> {{ $ticketDetalle->cargo }}</p>
                    <p><strong>Estado:</strong> {{ ucfirst($ticketDetalle->estado) }}</p>
                    <p><strong>Prioridad:</strong> {{ ucfirst($ticketDetalle->prioridad) }}</p>
                    <p><strong>Descripción:</strong> {{ $ticketDetalle->descripcion }}</p>
                    


                    <h3 class="mt-4 font-semibold">Historial</h3>
                    <ul class="list-disc pl-6">
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
                            class="bg-gray-400 text-white px-4 py-2 rounded">Cerrar</button>
                    </div>
                </div>
            </div>
        @endif

    </div>