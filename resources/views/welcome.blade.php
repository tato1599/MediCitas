<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Medicitas</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="font-sans antialiased ">
    <div class=" ">
        <div class="relative flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div>
                <header class="w-full bg-white border-b border-gray-200 ">

                    <nav class="bg-white border-gray-200 px-4 lg:px-6 py-2.5 ">
                        <div class="grid grid-cols-3 items-center mx-auto">
                            <a href="https://flowbite.com" class="flex items-center lg:justify-center lg:order-2">
                                <img src="https://flowbite.com/docs/images/logo.svg" class="mr-3 h-6 sm:h-9"
                                    alt="Flowbite Logo" />
                                <span class="self-center text-xl font-semibold whitespace-nowrap ">Medicitas</span>
                            </a>
                            <div class="flex col-span-2 justify-end items-center lg:order-3 lg:col-span-1">


                                @if (Route::has('login'))
                                    <nav class="-mx-3 flex flex-1 justify-end">
                                        @auth
                                            <a href="{{ url('/dashboard') }}"
                                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-hidden focus-visible:ring-[#FF2D20] ">
                                                Inicio
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}"
                                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-hidden focus-visible:ring-[#FF2D20] ">
                                                Iniciar sesión
                                            </a>


                                        @endauth
                                    </nav>
                                @endif

                                <button data-collapse-toggle="mobile-menu-2" type="button"
                                    class="inline-flex items-center p-2 ml-1 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200  :bg-gray-700 :ring-gray-600"
                                    aria-controls="mobile-menu-2" aria-expanded="false">
                                    <span class="sr-only">Abrir menú principal</span>
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <svg class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="hidden col-span-3 justify-between items-center w-full lg:flex lg:w-auto lg:order-1 lg:col-span-1"
                                id="mobile-menu-2">
                                <ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0">
                                    <li>
                                        <a href="/#inicio"
                                            class="block py-2 pr-4 pl-3 border-b border-gray-100 text-primary-600  hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:p-0 :bg-gray-700 :text-primary-500 lg::bg-transparent "
                                            aria-current="page">Inicio</a>
                                    </li>
                                    <li>
                                        <a href="/#recursos"
                                            class="block py-2 pr-4 pl-3 text-gray-700 border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0  lg::text-white :bg-gray-700 :text-white lg::bg-transparent ">
                                            Recursos
                                        </a>
                                    </li>
                                    <li>
                                        <a href="/#precios"
                                            class="block py-2 pr-4 pl-3 text-gray-700 border-b border-gray-100 hover:bg-gray-50 lg:hover:bg-transparent lg:border-0 lg:hover:text-primary-700 lg:p-0  lg::text-white :bg-gray-700 :text-white lg::bg-transparent ">
                                            Precios
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </header>
                <section class="bg-white " name="inicio" id="inicio">
                    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
                        <div class="mr-auto place-self-center lg:col-span-7">
                            <h1
                                class="max-w-2xl mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl ">
                                Simplifica tus citas
                            </h1>
                            <p class="max-w-2xl mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl ">
                                Con nuestra plataforma, puedes gestionar tus citas de manera eficiente y sin
                                complicaciones. Desde la programación hasta el seguimiento, tenemos todo cubierto para
                                que puedas centrarte en lo que realmente importa: tu negocio.
                            </p>
                            <a href="{{ route('checkout') }}"
                                class="inline-flex items-center justify-center px-5 py-3 mr-3 text-basic font-medium text-center  rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 ">
                                Empezar
                                <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </a>

                        </div>
                        <div class="hidden lg:mt-0 lg:col-span-5 lg:flex">
                            <img class="rounded-lg border-2 border-basic"
                                src="https://d2lcsjo4hzzyvz.cloudfront.net/blog/wp-content/uploads/2022/05/13100505/Por-que%CC%81-ofrecer-citas-me%CC%81dicas-virtuales-a-tus-pacientes-.jpg"
                                alt="mockup">
                        </div>
                    </div>
                </section>

                <x-resources id="recursos" name="recursos" />

                <section id="precios" >
                <x-pricing  />
                </section>

                <x-footer />

            </div>
        </div>
    </div>
</body>

</html>
