<x-app-layout>
    <div class="w-full px-4 sm:px-6 lg:px-10 xl:px-16 py-6 space-y-8">
        @auth
            @php
                $pendiente  = $stats['pendiente']   ?? 0;
                $enProceso  = $stats['en_proceso']  ?? 0;
                $finalizado = $stats['finalizado']  ?? 0;
                $total      = $pendiente + $enProceso + $finalizado;
            @endphp

            {{-- Encabezado + acciones rápidas --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-semibold">Panel de control</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Estado general de los tickets y accesos rápidos a las secciones principales.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    {{-- Botón para crear un nuevo memorando / ticket --}}
                    <a href="{{ route('tickets.create') }}"
                       class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
                        {{-- Icono sencillo de "+" --}}
                        <span class="mr-1 text-lg leading-none">＋</span>
                        Nuevo memorando
                    </a>

                    {{-- Botón Panel de Administración (solo visible para admins) --}}
                    @role('admin')
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center px-4 py-2 rounded-lg bg-purple-600 text-white text-sm font-medium hover:bg-purple-700 transition">
                            {{-- Icono de engranaje sencillo --}}
                            <span class="mr-1 text-base leading-none">⚙</span>
                            Panel de Administración
                        </a>
                    @endrole
                </div>
            </div>

            {{-- Tarjetas de KPIs / navegación --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 xl:gap-6">
                {{-- Pendientes --}}
                <a href="{{ route('tickets.board') }}"
                   class="flex flex-col justify-between rounded-2xl border border-blue-100 bg-white p-4 sm:p-5 shadow-sm hover:border-blue-300 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Tickets pendientes
                            </p>
                            <p class="mt-2 text-3xl font-semibold text-gray-800">
                                {{ number_format($pendiente) }}
                            </p>
                        </div>
                        {{-- Icono --}}
                        <div class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-blue-50">
                            <span class="text-blue-500 text-lg">⏳</span>
                        </div>
                    </div>
                    <p class="mt-3 text-xs font-medium text-blue-600">
                        Ver listado →
                    </p>
                </a>

                {{-- En proceso --}}
                <a href="{{ route('tickets.en_proceso') }}"
                   class="flex flex-col justify-between rounded-2xl border border-amber-100 bg-white p-4 sm:p-5 shadow-sm hover:border-amber-300 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Tickets en proceso
                            </p>
                            <p class="mt-2 text-3xl font-semibold text-gray-800">
                                {{ number_format($enProceso) }}
                            </p>
                        </div>
                        <div class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-amber-50">
                            <span class="text-amber-500 text-lg">🔄</span>
                        </div>
                    </div>
                    <p class="mt-3 text-xs font-medium text-amber-600">
                        Ver listado →
                    </p>
                </a>

                {{-- Finalizados --}}
                <a href="{{ route('tickets.finalizados') }}"
                   class="flex flex-col justify-between rounded-2xl border border-green-100 bg-white p-4 sm:p-5 shadow-sm hover:border-green-300 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Tickets finalizados
                            </p>
                            <p class="mt-2 text-3xl font-semibold text-gray-800">
                                {{ number_format($finalizado) }}
                            </p>
                        </div>
                        <div class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-green-50">
                            <span class="text-green-500 text-lg">✅</span>
                        </div>
                    </div>
                    <p class="mt-3 text-xs font-medium text-green-600">
                        Ver listado →
                    </p>
                </a>

                {{-- Total --}}
                <div class="flex flex-col justify-between rounded-2xl border border-slate-100 bg-white p-4 sm:p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Total de tickets
                            </p>
                            <p class="mt-2 text-3xl font-semibold text-gray-800">
                                {{ number_format($total) }}
                            </p>
                        </div>
                        <div class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-50">
                            <span class="text-slate-500 text-lg">📊</span>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-gray-400">
                        Suma de pendientes, en proceso y finalizados.
                    </p>
                </div>
            </div>

            {{-- Zona de indicadores y últimos movimientos --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 xl:gap-8">
                {{-- Indicadores por usuario --}}
                <div class="rounded-2xl border bg-white p-4 sm:p-5 shadow-sm">
                    <h2 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-slate-500 text-sm">
                            👥
                        </span>
                        Indicadores por usuario
                    </h2>

                    @if(isset($indicadoresPorUsuario) && $indicadoresPorUsuario->count())
                        <div class="border rounded-xl overflow-hidden">
                            <table class="min-w-full text-xs">
                                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wide">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Usuario</th>
                                        <th class="px-3 py-2 text-center">Pend.</th>
                                        <th class="px-3 py-2 text-center">Proc.</th>
                                        <th class="px-3 py-2 text-center">Fin.</th>
                                        <th class="px-3 py-2 text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($indicadoresPorUsuario as $row)
                                        @php
                                            $nombre = optional($row->asignado)->name ?? 'Sin responsable';
                                            $inicial = mb_substr($nombre, 0, 1, 'UTF-8');
                                        @endphp
                                        <tr>
                                            <td class="px-3 py-2 text-xs text-gray-700">
                                                <div class="flex items-center gap-2">
                                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-slate-100 text-[11px] font-semibold text-slate-600">
                                                        {{ $inicial }}
                                                    </span>
                                                    <span class="truncate">
                                                        {{ $nombre }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-center text-gray-700">
                                                {{ $row->pendientes }}
                                            </td>
                                            <td class="px-3 py-2 text-center text-gray-700">
                                                {{ $row->en_proceso }}
                                            </td>
                                            <td class="px-3 py-2 text-center text-gray-700">
                                                {{ $row->finalizados }}
                                            </td>
                                            <td class="px-3 py-2 text-center font-semibold text-gray-900">
                                                {{ $row->total }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-xs text-gray-500">
                            Aún no hay datos de responsables asignados para mostrar indicadores.
                        </p>
                    @endif
                </div>

                {{-- Últimos movimientos --}}
                <div class="rounded-2xl border bg-white p-4 sm:p-5 shadow-sm">
                    <h2 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-slate-100 text-slate-500 text-sm">
                            🕒
                        </span>
                        Últimos movimientos
                    </h2>

                    @if(isset($latestTickets) && $latestTickets->count())
                        <ul class="divide-y text-xs">
                            @foreach ($latestTickets as $ticket)
                                <li class="py-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                    <div class="space-y-1">
                                        <p class="font-medium text-gray-800">
                                            {{ $ticket->titulo ?? 'Sin asunto' }}
                                        </p>
                                        <p class="text-[11px] text-gray-500">
                                            {{ optional($ticket->created_at)->format('d/m/Y H:i') }}
                                            ·
                                            Creado por:
                                            <span class="font-medium text-gray-700">
                                                {{ optional($ticket->creador)->name ?? 'Desconocido' }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @php
                                            $estado = $ticket->estado ?? 'desconocido';
                                            $badgeClasses = match($estado) {
                                                'pendiente'   => 'bg-blue-100 text-blue-700',
                                                'en_proceso'  => 'bg-amber-100 text-amber-700',
                                                'finalizado'  => 'bg-green-100 text-green-700',
                                                'validacion'  => 'bg-sky-100 text-sky-700',
                                                default       => 'bg-slate-100 text-slate-600',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium {{ $badgeClasses }}">
                                            {{ ucfirst(str_replace('_', ' ', $estado)) }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-xs text-gray-500">
                            No hay registros recientes de tickets.
                        </p>
                    @endif
                </div>
            </div>
        @endauth
    </div>
</x-app-layout>
