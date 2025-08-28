<div class="p-4 bg-white rounded-xl shadow">
    <h3 class="font-semibold mb-3">Nueva novedad</h3>

    {{-- Notificación simple --}}
    <div 
        x-data="{ show: false, msg: '' }"
        x-on:notify.window="show = true; msg = $event.detail.msg; setTimeout(() => show = false, 3000)">
        <div 
            x-show="show"
            x-transition
            class="mb-3 px-3 py-2 rounded bg-green-100 text-green-700 text-sm">
            <span x-text="msg"></span>
        </div>
    </div>

    <form wire:submit.prevent="crear">
    {{-- Puesto --}}
    <div class="mb-2">
        <input wire:model.defer="puesto" type="text" class="border rounded px-3 py-2 w-full" placeholder="Puesto">
        @error('puesto') 
            <div class="text-red-600 text-sm">El puesto es obligatorio</div> 
        @enderror
    </div>

    

    {{-- Descripción --}}
    <div class="mb-2">
        <textarea wire:model.defer="descripcion" class="border rounded px-3 py-2 w-full" rows="3" placeholder="Descripción"></textarea>
        @error('descripcion')
            <div class="text-red-600 text-sm">La descripción es obligatoria</div>
        @enderror
    </div>

    {{-- Prioridad --}}
    <div class="mb-3">
        <select wire:model.defer="prioridad" class="border rounded px-3 py-2 w-full">
            <option value="">Selecciona prioridad</option>
            <option value="urgente">Urgente</option>
            <option value="alta">Alta</option>
            <option value="media">Media</option>
            <option value="baja">Baja</option>
        </select>
        @error('prioridad')
            <div class="text-red-600 text-sm">Debes seleccionar una prioridad</div>
        @enderror
    </div>

    <button type="submit" class="px-4 py-2 rounded bg-gray-800 text-white" wire:loading.attr="disabled">
        Crear
    </button>
</form>

</div>
