<!-- resources/views/livewire/shared/user-form.blade.php -->
<!-- Inputs reutilizables para crear/editar usuarios -->
<div>
    <!-- Name -->
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input wire:model.defer="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <!-- Email -->
    <div class="mt-4">
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input wire:model.defer="email" id="email" class="block mt-1 w-full" type="email" name="email" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <!-- Password -->
    <div class="mt-4">
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input wire:model.defer="password" id="password" class="block mt-1 w-full"
                      type="password"
                      name="password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <!-- Confirm Password -->
    <div class="mt-4">
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input wire:model.defer="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                      type="password"
                      name="password_confirmation" />
    </div>
</div>
