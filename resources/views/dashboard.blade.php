<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 space-y-6">
        @auth
            {{-- Formulario --}}
            @livewire('tickets.form')

            <div class="flex gap-2">
                
                {{-- Botón para tickets pendientes --}}
                <a href="{{ route('tickets.board') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Tickets pendientes
                </a>

                {{-- Botón para tickets en proceso --}}
                <a href="{{ route('tickets.en_proceso') }}"
                   class="px-4 py-2 bg-amber-500 text-white rounded hover:bg-amber-600">
                    Tickets en proceso
                </a>

                {{-- Botón ver tickets --}}
                <a href="{{ route('tickets.finalizados') }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Tickets Finalizados
                </a>

                {{-- Botón Panel de Administración (solo visible para admins) --}}
                @role('admin')
                    <a href="{{ route('admin.users.index') }}" 
                       class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                        Panel de Administración
                    </a>
                @endrole
            </div>

        @endauth
    </div>
</x-app-layout>
