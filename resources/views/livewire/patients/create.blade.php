
<div class="p-6">


<form>
    <div class="mb-4">
        <label for="name" class="block text-gray-700 text-sm font-medium mb-2 font-sans">Nombre</label>
        <input type="text" id="name" wire:model="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-basic leading-tight focus:border-basic border-basic" required>
    </div>
    <div class="mb-4">
        <label for="lastName" class="block text-gray-700 text-sm font-medium mb-2 font-sans">Apellidos</label>
        <input type="text" id="lastName" wire:model="lastName" class="shadow appearance-none border rounded w-full py-2 px-3 text-basic leading-tight focus:border-basic border-basic" required>
    </div>
    <div class="mb-4">
        <label for="phone" class="block text-gray-700 text-sm font-medium mb-2 font-sans">Teléfono (Opcional)</label>
        <input type="text" id="phone" wire:model="phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-basic leading-tight  focus:border-basic border-basic" required>
    </div>
    <div class="mb-4">
        <label for="email" class="block text-gray-700 text-sm font-medium mb-2 font-sans">Email (Opcional)</label>
        <input type="email" id="email" wire:model="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-basic leading-tightfocus:border-basic border-basic " required>
    </div>
</form>

<div class="flex items-center w-full justify-between mt-4">
    <button wire:click="store" class="bg-basic hover:bg-blue-700 text-white font-medium font-sans w-full py-2 rounded focus:outline-none focus:shadow-outline ">
        Guardar
    </button>

</div>

</div>

