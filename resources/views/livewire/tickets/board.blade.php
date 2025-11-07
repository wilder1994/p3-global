@php
    use Illuminate\Support\Str;
@endphp

<div class="flex flex-col gap-4 h-full w-full">

    {{-- Barra superior: volver al panel + buscador + tarjetas --}}
    <div class="sticky top-0 z-10 space-y-3 bg-slate-50/80 backdrop-blur pb-3">

        {{-- 🔙 Volver al panel --}}
        <div class="flex justify-end">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-3 py-1.5 rounded-full border border-slate-200 bg-white text-[11px] sm:text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                ← Volver al panel
            </a>
        </div>

        {{-- 🔍 Buscador (funciona con updatedSearch hook) --}}
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm px-3 py-2 sm:px-4 sm:py-3">
            <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                <div class="flex-1 flex items-center gap-2">
                    <input
                        type="text"
                        wire:model.live="search"
                        wire:input.debounce.500ms="filtrar"
                        placeholder="Buscar por asunto, descripción, prioridad, estado o creador..."
                        autocomplete="off"
                        spellcheck="true"
                        class="w-full px-3 py-2 text-sm border border-slate-200 rounded-xl
                            focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>
            </div>
        </div>

        {{-- 📊 Tarjetas resumen por estado (mismas del dashboard y navegables) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach ([
                'pendiente'   => ['label' => 'Tickets pendientes',  'color' => 'blue',   'icon' => '⏳', 'route' => 'tickets.board'],
                'en_proceso'  => ['label' => 'Tickets en proceso',  'color' => 'amber',  'icon' => '🔄', 'route' => 'tickets.en_proceso'],
                'finalizado'  => ['label' => 'Tickets finalizados', 'color' => 'green',  'icon' => '✅', 'route' => 'tickets.finalizados'],
            ] as $key => $info)
                @php
                    $count    = $conteos[$key] ?? 0;
                    $isActive = in_array($key, $estadosVisibles ?? []);

                    $baseBorder = match($info['color']) {
                        'blue'  => 'border-blue-100',
                        'amber' => 'border-amber-100',
                        'green' => 'border-green-100',
                        default => 'border-slate-100',
                    };

                    $iconBg = match($info['color']) {
                        'blue'  => 'bg-blue-50 text-blue-500',
                        'amber' => 'bg-amber-50 text-amber-500',
                        'green' => 'bg-green-50 text-green-500',
                        default => 'bg-slate-50 text-slate-500',
                    };

                    $borderClass = $isActive ? 'border-blue-400 shadow-md' : $baseBorder;
                @endphp

                <a href="{{ route($info['route']) }}"
                   class="bg-white rounded-2xl border {{ $borderClass }} shadow-sm px-4 py-3 flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">
                            {{ $info['label'] }}
                        </p>
                        <p class="mt-1 text-2xl font-semibold text-gray-800">
                            {{ $count }}
                        </p>
                    </div>
                    <div class="inline-flex h-9 w-9 items-center justify-center rounded-full {{ $iconBg }}">
                        <span class="text-lg">{{ $info['icon'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- 📋 Listado general de tickets --}}
    <div class="flex-1 bg-white shadow-sm rounded-2xl border border-slate-100 p-3 sm:p-4 overflow-auto">
        <h3 class="font-semibold mb-3 text-gray-800 text-sm sm:text-base">{{ $tituloTabla ?? 'Listado de tickets' }}</h3>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-xs sm:text-sm border-collapse table-auto">
                <thead>
                    <tr class="bg-slate-50 text-slate-600">
                        <th class="p-2 border text-left w-32 whitespace-nowrap">Fecha</th>
                        <th class="p-2 border text-left w-40 whitespace-nowrap">Creado</th>
                        <th class="p-2 border text-left w-56">Asunto</th>
                        <th class="p-2 border text-left w-56">Puesto</th>
                        <th class="p-2 border text-left w-[40%]">Descripción</th>
                        <th class="p-2 border text-left w-48">Asignado a</th>
                        <th class="p-2 border text-left w-32">Prioridad</th>
                        <th class="p-2 border text-center w-24">Detalles</th>
                        <th class="p-2 border text-center w-32">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ticketsPlanos as $t)
                        @php
                            // Colores por prioridad (un poco más intensos)
                            $rowColor = match(strtolower($t->prioridad)) {
                                'urgente' => 'bg-red-200',
                                'alta'    => 'bg-red-100',
                                'media'   => 'bg-yellow-100',
                                'baja'    => 'bg-green-100',
                                default   => 'bg-white'
                            };

                            $acciones = [
                                'pendiente' => [
                                    'texto'  => 'Pasar a en proceso',
                                    'estado' => 'en_proceso',
                                    'cambio' => true,
                                    'color'  => 'bg-blue-600 hover:bg-blue-700',
                                ],
                                'en_proceso' => [
                                    'texto'  => 'Agregar comentario',
                                    'estado' => 'en_proceso',
                                    'cambio' => false,
                                    'color'  => 'bg-amber-500 hover:bg-amber-600',
                                ],
                            ];
                            $accion = $acciones[$t->estado] ?? null;

                            $badgeEstadoClasses = match($t->estado) {
                                'pendiente'  => 'bg-blue-100 text-blue-700',
                                'en_proceso' => 'bg-amber-100 text-amber-700',
                                'finalizado' => 'bg-green-100 text-green-700',
                                'validacion' => 'bg-sky-100 text-sky-700',
                                default      => 'bg-slate-100 text-slate-600',
                            };

                            $badgePrioridadClasses = match(strtolower($t->prioridad)) {
                                'urgente' => 'bg-red-100 text-red-700',
                                'alta'    => 'bg-orange-100 text-orange-700',
                                'media'   => 'bg-yellow-100 text-yellow-700',
                                'baja'    => 'bg-green-100 text-green-700',
                                default   => 'bg-slate-100 text-slate-600',
                            };
                        @endphp

                        <tr class="{{ $rowColor }} hover:bg-slate-100 transition-colors">
                            <td class="p-2 border align-top whitespace-nowrap">
                                {{ $t->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-2 border align-top whitespace-nowrap">
                                {{ Str::title(Str::lower($t->creador?->name ?? '---')) }}
                            </td>
                            <td class="p-2 border align-top">
                                {{ Str::title(Str::lower($t->titulo)) }}
                            </td>
                            <td class="p-2 border align-top">
                                {{ Str::title(Str::lower($t->puesto)) }}
                            </td>
                            <td class="p-2 border align-top w-[40%]">
                                <div class="text-[11px] sm:text-xs leading-snug">
                                    {{ $t->descripcion }}
                                </div>
                            </td>
                            <td class="p-2 border align-top whitespace-nowrap">
                                {{ Str::title(Str::lower($t->asignado?->name ?? 'Sin asignar')) }}
                            </td>
                            <td class="p-2 border align-top">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $badgePrioridadClasses }}">
                                    {{ Str::title(Str::lower($t->prioridad)) }}
                                </span>
                            </td>
                            <td class="p-2 border text-center align-top">
                                <button
                                    wire:click="verDetalles({{ $t->id }})"
                                    class="px-3 py-1 rounded-lg bg-slate-700 text-white text-xs sm:text-sm hover:bg-slate-800 transition whitespace-nowrap">
                                    Ver
                                </button>
                            </td>
                            <td class="p-2 border text-center align-top">
                                <div class="flex flex-col items-center gap-2">
                                    @if ($accion)
                                        {{-- SOLO el botón, sin badge extra (no redundante) --}}
                                        <button
                                            wire:click="confirmarCambioEstado({{ $t->id }}, '{{ $accion['estado'] }}', @json($accion['cambio']))"
                                            class="px-3 py-1 rounded-lg text-white text-[11px] sm:text-xs {{ $accion['color'] }} transition whitespace-nowrap">
                                            {{ $accion['texto'] }}
                                        </button>
                                    @else
                                        {{-- Si no hay acción, mostramos badge con el estado --}}
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $badgeEstadoClasses }}">
                                            {{ Str::title(Str::lower(str_replace('_',' ',$t->estado))) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center p-4 text-gray-500">
                                {{ $mensajeVacio ?? 'No hay tickets registrados' }}
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
            <div class="bg-white rounded-2xl shadow-lg p-6
                        w-[90%] sm:w-[80%] md:w-[500px] lg:w-[600px]
                        max-h-[90vh] overflow-y-auto mx-auto border border-slate-100">

                <h2 class="text-lg font-bold mb-4 text-gray-800">Confirmar cambio de estado</h2>

                @if($cambioEstado)
                    <p class="mb-3 text-gray-700 text-sm">
                        Este ticket pasará al estado:
                        <span class="font-semibold capitalize text-blue-600">
                            {{ str_replace('_',' ', $nuevoEstado) }}
                        </span>
                    </p>
                @else
                    <p class="mb-3 text-gray-700 text-sm">
                        Se registrará un comentario sin modificar el estado actual del ticket.
                    </p>
                @endif

                {{-- Comentario --}}
                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    Comentario
                </label>
                <textarea
                    wire:model="comentario"
                    placeholder="Debe escribir un comentario obligatorio"
                    class="w-full border border-slate-200 rounded-xl p-2 text-sm mb-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>

                @error('comentario')
                    <div class="text-red-600 text-xs mb-3">{{ $message }}</div>
                @enderror

                {{-- Select de responsable --}}
                <div class="mb-4 mt-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Asignar responsable</label>
                    <select
                        wire:model.defer="responsable"
                        class="border border-slate-200 rounded-xl px-3 py-2 w-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                        <div class="text-red-600 text-xs mt-1">Debe asignar un responsable</div>
                    @enderror
                </div>

                {{-- Botones --}}
                <div class="flex flex-wrap justify-end gap-2 mt-4">
                    <button
                        wire:click="$set('mostrarModal', false)"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                        Cancelar
                    </button>

                    <button
                        wire:click="guardarCambioEstado"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        {{ $cambioEstado ? 'Confirmar' : 'Guardar comentario' }}
                    </button>

                    <button
                        wire:click="finalizarTicketDesdeModal"
                        class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm">
                        Finalizar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de detalles del ticket --}}
    @if($mostrarModalDetalles && $ticketDetalle)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-lg p-6
                        w-[95%] sm:w-[85%] md:w-[700px] lg:w-[900px]
                        max-h-[90vh] overflow-y-auto mx-auto border border-slate-100">
                <h2 class="text-lg font-bold mb-4 text-gray-800">
                    Detalles del ticket #{{ $ticketDetalle->id }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700">
                    <p><strong>Fecha de creación:</strong> {{ $ticketDetalle->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Creado por:</strong> {{ $ticketDetalle->creador?->name ?? '—' }}</p>
                    <p><strong>Asunto:</strong> {{ $ticketDetalle->titulo }}</p>
                    <p><strong>Puesto:</strong> {{ $ticketDetalle->puesto }}</p>
                    <p><strong>Nombre del guarda:</strong> {{ $ticketDetalle->nombre_guarda }}</p>
                    <p><strong>Cédula del guarda:</strong> {{ $ticketDetalle->cedula_guarda }}</p>
                    <p><strong>Cargo:</strong> {{ $ticketDetalle->cargo }}</p>
                    <p><strong>Estado:</strong> {{ ucfirst($ticketDetalle->estado) }}</p>
                    <p><strong>Prioridad:</strong> {{ ucfirst($ticketDetalle->prioridad) }}</p>
                </div>

                <div class="mt-3 text-sm text-gray-700">
                    <p><strong>Descripción:</strong></p>
                    <p class="mt-1 whitespace-pre-line">{{ $ticketDetalle->descripcion }}</p>
                </div>

                <h3 class="mt-4 font-semibold text-gray-800 text-sm">Historial</h3>
                <ul class="list-disc pl-5 text-xs text-gray-700 space-y-1 mt-1">
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
                    <button
                        wire:click="$set('mostrarModalDetalles', false)"
                        class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
