<div class="p-6">
    <form wire:submit.prevent="store" class="bg-white shadow-md rounded-lg px-8 pt-6 pb-8 mb-4 space-y-4">
        <div>
            <label for="name" class="block text-gray-700 text-sm font-medium mb-2 font-sans">Nombre</label>
            <input type="text" id="first_name" wire:model="first_name"
                class="appearance-none border border-basic rounded w-full py-2 px-3 text-gray-800 font-sans leading-tight focus:outline-none focus:ring-2 focus:ring-primary transition"
                required>
        </div>

        <div>
            <label for="lastName" class="block text-gray-700 text-sm font-medium mb-2 font-sans">Apellidos</label>
            <input type="text" id="lastName" wire:model="last_name"
                class="appearance-none border border-basic rounded w-full py-2 px-3 text-gray-800 font-sans leading-tight focus:outline-none focus:ring-2 focus:ring-primary transition"
                required>
        </div>

        <div>
            <label for="birthdate" class="block text-gray-700 text-sm font-medium mb-2 font-sans">Fecha de Nacimiento</label>
            <input type="date" id="birthdate" wire:model="dob"
                class="appearance-none border border-basic rounded w-full py-2 px-3 text-gray-800 font-sans leading-tight focus:outline-none focus:ring-2 focus:ring-primary transition"
               >
        </div>

        <div>
            <label for="phone" class="block text-gray-700 text-sm font-medium mb-2 font-sans">Teléfono (Opcional)</label>
            <input type="text" id="phone" wire:model="phone"
                class="appearance-none border border-basic rounded w-full py-2 px-3 text-gray-800 font-sans leading-tight focus:outline-none focus:ring-2 focus:ring-primary transition">
        </div>

        <div>
            <label for="email" class="block text-gray-700 text-sm font-medium mb-2 font-sans">Email (Opcional)</label>
            <input type="email" id="email" wire:model="email"
                class="appearance-none border border-basic rounded w-full py-2 px-3 text-gray-800 font-sans leading-tight focus:outline-none focus:ring-2 focus:ring-primary transition">
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit"
                class="bg-primary hover:bg-blue-500 bg-basic text-white font-medium font-sans px-6 py-2 rounded shadow focus:outline-none focus:ring-2 focus:ring-primaryVariant transition w-full sm:w-auto">
                Guardar
            </button>
        </div>
    </form>
</div>
