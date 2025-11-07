<div class="max-w-lg mx-auto p-6 bg-white shadow rounded">
    <h2 class="text-xl font-bold mb-4">Crear Usuario</h2>

    @if (session()->has('message'))
        <div class="mb-4 p-2 bg-green-100 text-green-800 rounded">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        <!-- Nombre -->
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium">Nombre</label>
            <input type="text" wire:model="name" id="name"
                   class="w-full border rounded p-2">
            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium">Email</label>
            <input type="email" wire:model="email" id="email"
                   class="w-full border rounded p-2">
            @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Contraseña -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium">Contraseña</label>
            <input type="password" wire:model="password" id="password"
                   class="w-full border rounded p-2">
            @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Rol -->
        <div class="mb-4">
            <label for="role" class="block text-sm font-medium">Rol</label>
            <select wire:model="role" id="role" class="w-full border rounded p-2">
                <option value="">-- Selecciona un rol --</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                @endforeach
            </select>
            @error('role') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Botón -->
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Guardar Usuario
        </button>
        <a href="{{ route('admin.users.index') }}" 
                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancelar
            </a>
    </form>
</div>
