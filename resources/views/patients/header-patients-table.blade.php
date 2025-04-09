<div class="flex items-center justify-between gap-4 w-full px-4 py-2">
    <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="text-basic h-10 w-10" viewBox="0 0 24 24">
            <path fill="currentColor"
                d="m17.91,7c-.478,2.833-2.942,5-5.91,5s-5.431-2.167-5.91-5h11.819Zm.006-2c-.167-.979-.573-1.877-1.154-2.636l-3.912,2.636h5.066Zm-8.464,0L15.283.984c-.944-.62-2.072-.984-3.283-.984-2.967,0-5.431,2.167-5.91,5h3.362Zm7.048,9h-4.38l1.6,6h3.28v4h4v-5.5c0-2.481-2.019-4.5-4.5-4.5Zm-9,0h2.395l2.105,8h3v2H6c-1.654,0-3-1.346-3-3v-2.5c0-2.481,2.019-4.5,4.5-4.5Z" />
        </svg>
        <h1 class="text-2xl font-bold text-basic">Pacientes</h1>
    </div>

    <div class="flex items-center w-5/8 bg-white border border-basic rounded-xl px-4 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-basic me-2" viewBox="0 0 24 24">
            <path fill="currentColor" d="M23.707,22.293l-5.969-5.969a10.016,10.016,0,1,0-1.414,1.414l5.969,5.969a1,1,0,0,0,1.414-1.414ZM10,18a8,8,0,1,1,8-8A8.009,8.009,0,0,1,10,18Z"/>
        </svg>

        <input
            wire:model.debounce.300ms="search"
            type="text"
            placeholder="Buscar Pacientes..."
            class="w-full bg-white text-basic placeholder:text-gray-500 placeholder:italic font-sans border-none outline-none focus:ring-0 focus:outline-none focus:border-none"
        />
    </div>

    <a href="{{ route('patients.create') }}"
        class="bg-basic hover:bg-primaryVariant text-white font-medium px-4 py-2 rounded-md transition">
        + Nuevo Paciente
    </a>
</div>
