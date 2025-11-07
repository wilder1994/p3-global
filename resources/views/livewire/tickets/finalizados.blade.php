@php
    use Illuminate\Support\Str;
@endphp

<div class="flex flex-col gap-4 h-full w-full">

    {{-- Barra superior: volver al panel + buscador + tarjetas --}}
    <div class="sticky top-0 z-10 space-y-3 bg-slate-50/80 backdrop-blur pb-3">

        {{-- 🔙 Volver al panel --}}
        <div class="flex justify-end">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-3 py-1.5 rounded-full border border-slate-200 bg-white
                      text-[11px] sm:text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                ← Volver al panel
            </a>
        </div>

        {{-- 🔍 Buscador --}}
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
                <div wire:loading.delay wire:target="search" class="text-[11px] text-slate-400 sm:text-xs">
                    Buscando tickets…
                </div>
            </div>
        </div>

        {{-- 📊 Tarjetas resumen por estado (iguales al board) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @foreach ([
                'pendiente'   => ['label' => 'Tickets pendientes',  'color' => 'blue',   'icon' => '⏳', 'route' => 'tickets.board'],
                'en_proceso'  => ['label' => 'Tickets en proceso',  'color' => 'amber',  'icon' => '🔄', 'route' => 'tickets.en_proceso'],
                'finalizado'  => ['label' => 'Tickets finalizados', 'color' => 'green',  'icon' => '✅', 'route' => 'tickets.finalizados'],
            ] as $key => $info)
                @php
                    $count    = $conteos[$key] ?? 0;
                    $isActive = $key === 'finalizado';

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

    {{-- 📋 Tabla de tickets finalizados --}}
    <div class="flex-1 bg-white shadow-sm rounded-2xl border border-slate-100 p-3 sm:p-4 overflow-auto">
        <h3 class="font-semibold mb-3 text-gray-800 text-sm sm:text-base">
            Tickets finalizados
        </h3>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-xs sm:text-sm border-collapse table-auto">
                <thead>
                    <tr class="bg-slate-50 text-slate-600">
                        <th class="p-2 border text-left w-32 whitespace-nowrap">Fecha</th>
                        <th class="p-2 border text-left w-48 whitespace-nowrap">Título</th>
                        <th class="p-2 border text-left w-48 whitespace-nowrap">Puesto</th>
                        <th class="p-2 border text-left w-[40%]">Descripción</th>
                        <th class="p-2 border text-left w-32">Prioridad</th>
                        <th class="p-2 border text-left w-40 whitespace-nowrap">Responsable</th>
                        <th class="p-2 border text-left w-40 whitespace-nowrap">Creado por</th>
                        <th class="p-2 border text-center w-28">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $t)
                        @php
                            $prioridad = strtolower($t->prioridad);
                            $rowColor = match($prioridad) {
                                'urgente' => 'bg-red-100',
                                'alta'    => 'bg-red-50',
                                'media'   => 'bg-amber-50',
                                'baja'    => 'bg-emerald-50',
                                default   => 'bg-white',
                            };
                            $badgeClass = match($prioridad) {
                                'urgente' => 'bg-red-100 text-red-700',
                                'alta'    => 'bg-orange-100 text-orange-700',
                                'media'   => 'bg-amber-100 text-amber-800',
                                'baja'    => 'bg-emerald-100 text-emerald-700',
                                default   => 'bg-slate-100 text-slate-700',
                            };
                        @endphp

                        <tr class="{{ $rowColor }} hover:border-2 hover:border-slate-400 transition-all">
                            <td class="p-2 border align-top text-[11px] sm:text-xs">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-2 border align-top">{{ Str::title(Str::lower($t->titulo)) }}</td>
                            <td class="p-2 border align-top">{{ Str::title(Str::lower($t->puesto)) }}</td>
                            <td class="p-2 border align-top text-[13px] leading-snug">{{ $t->descripcion }}</td>
                            <td class="p-2 border align-top">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $badgeClass }}">
                                    {{ Str::title(Str::lower($t->prioridad)) }}
                                </span>
                            </td>
                            <td class="p-2 border align-top">{{ Str::title(Str::lower($t->asignado?->name ?? 'Sin asignar')) }}</td>
                            <td class="p-2 border align-top">{{ Str::title(Str::lower($t->creador?->name ?? '---')) }}</td>
                            <td class="p-2 border text-center align-top">
                                <button
                                    wire:click="verDetalles({{ $t->id }})"
                                    class="px-3 py-1.5 rounded-full bg-slate-700 text-white text-[11px] sm:text-xs hover:bg-slate-800 transition">
                                    Ver detalles
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center p-4 text-gray-500">No hay tickets finalizados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal de detalles --}}
    @if($mostrarModalDetalles && $ticketDetalle)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-lg p-6 
                        w-[95%] sm:w-[85%] md:w-[700px] lg:w-[900px] 
                        max-h-[90vh] overflow-y-auto mx-auto border border-slate-100">

                <h2 class="text-lg font-bold mb-4 text-gray-800">
                    Detalles del ticket #{{ $ticketDetalle->id }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700">
                    <p><strong>Título:</strong> {{ $ticketDetalle->titulo }}</p>
                    <p><strong>Puesto:</strong> {{ $ticketDetalle->puesto }}</p>
                    <p><strong>Prioridad:</strong> {{ ucfirst($ticketDetalle->prioridad) }}</p>
                    <p><strong>Estado:</strong> {{ ucfirst($ticketDetalle->estado) }}</p>
                    <p><strong>Creado por:</strong> {{ $ticketDetalle->creador?->name ?? '—' }}</p>
                    <p><strong>Responsable:</strong> {{ $ticketDetalle->asignado?->name ?? 'Sin asignar' }}</p>
                    <p class="md:col-span-2"><strong>Fecha de creación:</strong> {{ $ticketDetalle->created_at->format('d/m/Y H:i') }}</p>
                    <p class="md:col-span-2"><strong>Descripción:</strong> {{ $ticketDetalle->descripcion }}</p>
                </div>

                <h3 class="mt-4 mb-2 font-semibold text-sm text-gray-800">Historial</h3>
                <ul class="list-disc pl-6 text-sm text-gray-700 space-y-1">
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
