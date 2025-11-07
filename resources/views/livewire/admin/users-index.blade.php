<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Usuarios</h1>
        <a href="{{ route('admin.users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
            Crear usuario
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 text-red-800 p-2 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <table class="table-auto w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 border">ID</th>
                <th class="px-4 py-2 border">Nombre</th>
                <th class="px-4 py-2 border">Email</th>
                <th class="px-4 py-2 border">Roles</th>
                <th class="px-4 py-2 border">Estado</th>
                <th class="px-4 py-2 border">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td class="px-4 py-2 border">{{ $user->id }}</td>
                    <td class="px-4 py-2 border">{{ $user->name }}</td>
                    <td class="px-4 py-2 border">{{ $user->email }}</td>
                    <td class="px-4 py-2 border">
                        {{ $user->roles->pluck('name')->join(', ') }}
                    </td>
                    <td class="px-4 py-2 border">
                        <!-- Switch ON/OFF -->
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   class="sr-only peer"
                                   wire:click="toggleActive({{ $user->id }})"
                                   {{ $user->is_active ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 
                                        peer-focus:ring-blue-500 rounded-full peer
                                        peer-checked:after:translate-x-full peer-checked:after:border-white 
                                        after:content-[''] after:absolute after:top-0.5 after:left-[2px] 
                                        after:bg-white after:border-gray-300 after:border after:rounded-full 
                                        after:h-5 after:w-5 after:transition-all 
                                        peer-checked:bg-green-500 relative">
                            </div>
                            <span class="ml-2 text-sm font-medium text-gray-900">
                                {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </label>
                    </td>
                    <td class="px-4 py-2 border flex space-x-2">
                        <!-- Botón Editar -->
                        <a href="{{ route('admin.users.edit', $user->id) }}"
                        class="bg-yellow-500 text-white px-2 py-1 rounded">
                            Editar
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
