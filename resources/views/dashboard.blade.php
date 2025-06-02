<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-lg shadow p-6">
                <h1 class="text-3xl font-extrabold text-gray-900">
                    ¡Bienvenido, {{ Auth::user()->name }}!
                </h1>
                <p class="mt-3 text-gray-700 text-lg">
                    Nos alegra tenerte de vuelta. Desde aquí puedes gestionar tu información y acceder a todas las herramientas disponibles.
                </p>
            </div>
        </div>
    </div>

</x-app-layout>
