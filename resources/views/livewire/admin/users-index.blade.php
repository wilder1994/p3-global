<div class="flex flex-col gap-4 h-full w-full px-4 sm:px-8 md:px-10 lg:px-14 py-4 sm:py-6">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg sm:text-xl font-semibold text-slate-800">
                Usuarios
            </h1>
            <p class="text-xs sm:text-sm text-slate-500">
                Administración de cuentas, roles y estado de acceso al sistema.
            </p>
        </div>

        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-full bg-blue-600 text-white
                  text-xs sm:text-sm font-medium shadow hover:bg-blue-700 transition">
            <span class="text-lg leading-none">＋</span>
            <span>Crear usuario</span>
        </a>
    </div>

    {{-- Mensajes flash --}}
    @if (session('success'))
        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800
                    text-xs sm:text-sm px-3 py-2 rounded-xl">
            <span class="text-base">✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700
                    text-xs sm:text-sm px-3 py-2 rounded-xl">
            <span class="text-base">⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Tabla de usuarios --}}
    <div class="bg-white shadow-sm rounded-2xl border border-slate-100 p-3 sm:p-4 overflow-x-auto">
        <table class="min-w-full text-xs sm:text-sm border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-600 text-[11px] sm:text-xs uppercase tracking-wide">
                    <th class="px-3 py-2 border border-slate-100 text-left w-12">ID</th>
                    <th class="px-3 py-2 border border-slate-100 text-left">Nombre</th>
                    <th class="px-3 py-2 border border-slate-100 text-left">Email</th>
                    <th class="px-3 py-2 border border-slate-100 text-left whitespace-nowrap">Roles</th>
                    <th class="px-3 py-2 border border-slate-100 text-left">Estado</th>
                    <th class="px-3 py-2 border border-slate-100 text-center w-28">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $user)
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-3 py-2 border border-slate-100 align-middle text-slate-500">
                            {{ $user->id }}
                        </td>

                        <td class="px-3 py-2 border border-slate-100 align-middle text-slate-800">
                            {{ $user->name }}
                        </td>

                        <td class="px-3 py-2 border border-slate-100 align-middle text-slate-700">
                            {{ $user->email }}
                        </td>

                        <td class="px-3 py-2 border border-slate-100 align-middle">
                            @php
                                $roles = $user->roles->pluck('name')->join(', ');
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-[11px] sm:text-xs">
                                {{ $roles ?: 'Sin rol' }}
                            </span>
                        </td>

                        <td class="px-3 py-2 border border-slate-100 align-middle">
                            <div class="flex items-center gap-2">
                                {{-- Switch ON/OFF --}}
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           class="sr-only peer"
                                           wire:click="toggleActive({{ $user->id }})"
                                           {{ $user->is_active ? 'checked' : '' }}>
                                    <div class="relative w-11 h-6 bg-slate-200 rounded-full peer
                                                peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500
                                                peer-checked:bg-emerald-500
                                                after:content-[''] after:absolute after:top-0.5 after:left-[2px]
                                                after:w-5 after:h-5 after:bg-white after:border after:border-slate-300
                                                after:rounded-full after:transition-all
                                                peer-checked:after:translate-x-full peer-checked:after:border-white">
                                    </div>
                                </label>

                                <span class="text-[11px] sm:text-xs font-medium {{ $user->is_active ? 'text-emerald-700' : 'text-slate-500' }}">
                                    {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </td>

                        <td class="px-3 py-2 border border-slate-100 align-middle text-center">
                            <div class="inline-flex gap-2">
                                {{-- Botón Editar --}}
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                   class="inline-flex items-center justify-center px-3 py-1.5 rounded-full
                                          bg-amber-500 text-white text-[11px] sm:text-xs font-medium
                                          hover:bg-amber-600 transition">
                                    Editar
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            class="px-4 py-6 text-center text-slate-500 text-xs sm:text-sm">
                            No hay usuarios registrados todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
