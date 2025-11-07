
    <div class="min-h-[calc(100vh-120px)] w-full bg-slate-100/80">
        <div class="mx-auto flex h-full max-w-7xl flex-col gap-6 px-4 py-6">

            @php
                $claveEstados = implode(',', $estadosVisibles);
                $descripcion = match ($claveEstados) {
                    'pendiente' => 'Gestiona los tickets que están esperando ser atendidos.',
                    'en_proceso' => 'Da seguimiento a los tickets que ya están en ejecución.',
                    default => 'Filtra y gestiona los tickets activos del equipo.',
                };
            @endphp

            {{-- 🔍 Buscador --}}
            <div class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm backdrop-blur">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-slate-900">Gestión de tickets</h1>
                        <p class="text-sm text-slate-500">{{ $descripcion }}</p>
                    </div>

                    <div class="flex w-full flex-col gap-2 md:w-auto md:flex-row">
                        <div class="relative flex-1 md:w-72">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35m1.35-4.65a6 6 0 11-12 0 6 6 0 0112 0z" />
                                </svg>
                            </span>
                            <input
                                type="text"
                                wire:model.debounce.500ms="search"
                                placeholder="Buscar por asunto, descripción o responsable"
                                class="w-full rounded-full border border-slate-200 bg-white py-2.5 pl-11 pr-4 text-sm text-slate-700 shadow-sm transition focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            />
                        </div>
                        <button
                            wire:click="loadTickets"
                            class="inline-flex items-center justify-center rounded-full bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-1"
                        >
                            Actualizar
                        </button>
                    </div>
                </div>
            </div>


            {{-- 📊 Tarjetas resumen por estado --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                @foreach ([
                    'pendiente' => ['label' => 'Pendientes', 'gradient' => 'from-amber-400 via-amber-500 to-amber-600'],
                    'en_proceso' => ['label' => 'En proceso', 'gradient' => 'from-blue-500 via-blue-600 to-blue-700'],
                    'finalizado' => ['label' => 'Finalizados', 'gradient' => 'from-emerald-500 via-emerald-600 to-emerald-700'],
                ] as $key => $config)
                    <div class="rounded-2xl bg-gradient-to-br {{ $config['gradient'] }} p-[1px] shadow">
                        <div class="rounded-[18px] bg-white/95 p-5 text-center">
                            <p class="text-sm font-medium text-slate-500">{{ $config['label'] }}</p>
                            <p class="mt-2 text-4xl font-semibold text-slate-900">{{ $conteos[$key] ?? 0 }}</p>
                        </div>
                    </div>
                @endforeach
            </div>


            {{-- 📋 Listado general de tickets con scroll --}}
            <div class="flex-1 rounded-3xl border border-slate-200 bg-white/95 p-6 shadow-lg backdrop-blur">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">{{ $tituloTabla }}</h2>
                        <p class="text-sm text-slate-500">{{ count($ticketsPlanos) }} registros encontrados</p>
                    </div>

                    <div class="flex gap-2 text-xs font-medium uppercase tracking-wide text-slate-400">
                        <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1 text-amber-600">
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span> Alta prioridad
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-blue-600">
                            <span class="h-2 w-2 rounded-full bg-blue-500"></span> Media prioridad
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-emerald-600">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Baja prioridad
                        </span>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-100 shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-900 text-left text-xs font-semibold uppercase tracking-wide text-white">
                            <tr>
                                <th scope="col" class="px-5 py-3">Fecha</th>
                                <th scope="col" class="px-5 py-3">Creado</th>
                                <th scope="col" class="px-5 py-3">Asunto</th>
                                <th scope="col" class="px-5 py-3">Puesto</th>
                                <th scope="col" class="px-5 py-3">Descripción</th>
                                <th scope="col" class="px-5 py-3">Asignado a</th>
                                <th scope="col" class="px-5 py-3">Prioridad</th>
                                <th scope="col" class="px-5 py-3 text-center">Detalles</th>
                                <th scope="col" class="px-5 py-3 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($ticketsPlanos as $t)
                                @php
                                    $prioridadColor = match(strtolower($t->prioridad)) {
                                        'urgente' => 'text-red-700 bg-red-50 ring-red-200',
                                        'alta'    => 'text-amber-700 bg-amber-50 ring-amber-200',
                                        'media'   => 'text-blue-700 bg-blue-50 ring-blue-200',
                                        'baja'    => 'text-emerald-700 bg-emerald-50 ring-emerald-200',
                                        default   => 'text-slate-700 bg-slate-50 ring-slate-200',
                                    };

                                    $acciones = [
                                        'pendiente' => [
                                            'etiqueta' => 'Pendiente',
                                            'estado' => 'en_proceso',
                                            'cambio' => true,
                                            'color' => 'bg-amber-500 hover:bg-amber-600 focus:ring-amber-300',
                                            'tooltip' => 'Agregar comentario y mover a En proceso',
                                        ],
                                        'en_proceso' => [
                                            'etiqueta' => 'En proceso',
                                            'estado' => 'en_proceso',
                                            'cambio' => false,
                                            'color' => 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-300',
                                            'tooltip' => 'Agregar comentario sin cambiar el estado',
                                        ],
                                    ];
                                    $accion = $acciones[$t->estado] ?? null;
                                @endphp

                                <tr class="transition hover:bg-blue-50/70">
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-900">{{ $t->creador?->name ?? '—' }}</td>
                                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $t->titulo }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $t->puesto }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $t->descripcion }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $t->asignado?->name ?? 'Sin asignar' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $prioridadColor }}">
                                            {{ ucfirst($t->prioridad) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <button
                                            wire:click="verDetalles({{ $t->id }})"
                                            class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-600 shadow-sm transition hover:border-blue-500 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-1"
                                        >
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Ver
                                        </button>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @if ($accion)
                                            <button
                                                wire:click="confirmarCambioEstado({{ $t->id }}, '{{ $accion['estado'] }}', @json($accion['cambio']))"
                                                class="inline-flex items-center justify-center gap-2 rounded-full px-5 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $accion['color'] }}"
                                                title="{{ $accion['tooltip'] }}"
                                            >
                                                <span class="h-2 w-2 rounded-full bg-white/80"></span>
                                                {{ $accion['etiqueta'] }}
                                            </button>
                                        @else
                                            <span class="inline-flex items-center justify-center rounded-full bg-slate-100 px-4 py-2 text-xs font-medium text-slate-400">
                                                Sin acciones
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-sm text-slate-500">
                                        {{ $mensajeVacio }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


        {{-- Modal de confirmación de estado --}}
        @if($mostrarModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 
                            w-[90%] sm:w-[80%] md:w-[500px] lg:w-[600px] 
                            max-h-[90vh] overflow-y-auto mx-auto">

                    <h2 class="text-lg font-bold mb-4">Confirmar cambio de estado</h2>

                    @if($cambioEstado)
                        <p class="mb-2 text-gray-700">
                            Este ticket pasará al estado:
                            <span class="font-semibold capitalize text-blue-600">
                                {{ str_replace('_',' ', $nuevoEstado) }}
                            </span>
                        </p>
                    @else
                        <p class="mb-2 text-gray-700">
                            Se registrará un comentario sin modificar el estado actual del ticket.
                        </p>
                    @endif

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
                            {{ $cambioEstado ? 'Confirmar' : 'Guardar comentario' }}
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