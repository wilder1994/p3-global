<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Editar usuario</h2>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="update" class="space-y-4">
        {{-- Nombre --}}
        <div>
            <label class="block font-medium">Nombre</label>
            <input type="text" wire:model="name"
                   class="w-full border rounded px-3 py-2">
            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block font-medium">Email</label>
            <input type="email" wire:model="email"
                   class="w-full border rounded px-3 py-2">
            @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Roles --}}
        <div>
            <label class="block font-medium">Roles</label>
            <select wire:model="roles" multiple
                    class="w-full border rounded px-3 py-2">
                @foreach ($allRoles as $role)
                    <option value="{{ $role }}">{{ $role }}</option>
                @endforeach
            </select>
            @error('roles') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Contraseña nueva --}}
        <div>
            <label class="block font-medium">Nueva contraseña</label>
            <input type="password" wire:model="password"
                   class="w-full border rounded px-3 py-2"
                   placeholder="Dejar en blanco si no deseas cambiar">
            @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Confirmar contraseña --}}
        <div>
            <label class="block font-medium">Confirmar contraseña</label>
            <input type="password" wire:model="password_confirmation"
                   class="w-full border rounded px-3 py-2"
                   placeholder="Repite la nueva contraseña">
        </div>

        {{-- Botones --}}
        <div class="flex justify-end space-x-2">
            <a href="{{ route('admin.users.index') }}"
               class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                Cancelar
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Guardar cambios
            </button>
        </div>
    </form>
</div>
