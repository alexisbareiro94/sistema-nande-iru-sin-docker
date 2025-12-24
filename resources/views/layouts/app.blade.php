<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.userId = {{ auth()->id() }};
        window.tenantId = {{ auth()->user()->tenant_id }}
    </script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>@yield('titulo', 'Mi Aplicación')</title>
</head>

<body class="bg-gris min-h-screen  flex flex-col">
    <div id="pdf-notificacion" class="fixed top-4 right-4 space-y-2 z-[9999]"></div>
    <div id="toast-container" class="fixed top-4 right-4 space-y-2 z-[9999]"></div>
    <div id="loading-container" class="fixed top-4 right-4 space-y-2 z-[9999]"></div>
    <div id="notificaciones" class="fixed top-4 right-4 space-y-2 z-[9999]"></div>
    @include('alertas.alerts')
    <main class="flex-grow grid grid-cols-1 md:grid-cols-5 gap-1 relative">

        {{-- Botón Toggle Aside (solo visible para admin) --}}
        @if (auth()->user()->role == 'admin')
            <button id="toggle-aside-btn"
                class="md:hidden fixed bottom-4 left-4 z-50 bg-black/10 backdrop-blur-sm shadow-lg rounded-full p-4 hover:bg-gray-100 transition-all duration-300">
                <svg id="toggle-icon-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg id="toggle-icon-close" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-6 hidden">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif

        {{-- Overlay para móvil --}}
        <div id="aside-overlay"
            class="fixed inset-0 bg-black/30 backdrop-blur-sm z-30 hidden transition-opacity duration-300"></div>

        <div id="aside-container" @class([
            'bg-gris pl-4 animate-fade-in transition-all duration-300 z-40 md:z-auto col-span-1 fixed inset-y-0 left-0 w-full md:relative md:w-auto md:inset-auto md:translate-x-0 -translate-x-full',
            'hidden' => auth()->user()->role != 'admin',
        ])>
            <aside class="col-span-1 p-4 transform transition-transform duration-300 h-full overflow-y-auto">
                @include('home.aside')
            </aside>
        </div>
        <section id="main-content" @class([
            'md:p-6 z-10 transition-all duration-300',
            'col-span-5' => auth()->user()->role != 'admin',
            'col-span-4' => auth()->user()->role == 'admin',
        ])>
            <div class="w-full mx-auto px-2 md:px-4 sm:px-6 lg:px-8 py-6 bg-gray-200 rounded-lg min-h-screen shadow-lg">
                <nav @class([
                    'text-sm font-semibold text-gray-700 mb-4',
                    'hidden' => request()->routeIs('home'),
                ]) aria-label="Breadcrumb">
                    <ol class="list-reset flex">
                        <li>
                            <a href="{{ route('home') }}" class="text-blue-500 hover:underline">Inicio</a>
                        </li>
                        @if (View::hasSection('ruta-anterior'))
                            <li><span class="mx-2">/</span></li>
                            <li class="text-gray-500">
                                <a href="@yield('url')">@yield('ruta-anterior')</a>
                            </li>
                        @endif
                        @if (View::hasSection('ruta-actual'))
                            <li><span class="mx-2">/</span></li>
                            <li class="text-gray-500">
                                @yield('ruta-actual')
                            </li>
                        @endif
                    </ol>
                </nav>
                @yield('contenido')
            </div>
        </section>

        @include('includes.cerrar-sesion')
    </main>
    @yield('js')
    <script src="{{ asset('js/add-producto.js') }}"></script>
    <script src="{{ asset('js/inventario.js') }}"></script>
    <script src="{{ asset('js/edit-producto.js') }}"></script>
    <script src="{{ asset('js/marca.js') }}"></script>
    <script src="{{ asset('js/categoria.js') }}"></script>
    <script src="{{ asset('js/filtros.js') }}"></script>
    <script src="{{ asset('js/historial-ventas.js') }}"></script>
    <script src="{{ asset('js/caja.js') }}"></script>
    <script src="{{ asset('js/procesar-caja.js') }}"></script>
    <script>
        if (sessionStorage.getItem('pdf-toast')) {
            toastLoading();
        }
    </script>
    @if (request()->routeIs('producto.update.view') || request()->routeIs('producto.update'))
        <script src="{{ asset('js/edit-productorep.js') }}"></script>
    @endif

    {{-- Script para Toggle del Aside --}}
    @if (auth()->user()->role == 'admin')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggleBtn = document.getElementById('toggle-aside-btn');
                const asideContainer = document.getElementById('aside-container');
                const asideOverlay = document.getElementById('aside-overlay');
                const mainContent = document.getElementById('main-content');
                const iconOpen = document.getElementById('toggle-icon-open');
                const iconClose = document.getElementById('toggle-icon-close');

                // En móvil siempre empieza oculto, en desktop depende de localStorage
                let isAsideVisible = false;

                // Función para mostrar el aside
                function showAside() {
                    asideContainer.classList.remove('-translate-x-full', 'md:-translate-x-full');
                    asideContainer.classList.add('translate-x-0');
                    asideContainer.classList.remove('hidden');

                    // Mostrar overlay en móvil
                    if (window.innerWidth < 768) {
                        asideOverlay.classList.remove('hidden');
                    }

                    // Actualizar iconos
                    iconOpen.classList.add('hidden');
                    iconClose.classList.remove('hidden');

                    // Ajustar contenido principal en desktop
                    if (window.innerWidth >= 768) {
                        mainContent.classList.remove('col-span-5');
                        mainContent.classList.add('col-span-4');
                    }

                    isAsideVisible = true;
                    localStorage.setItem('aside-visible', 'true');
                }

                // Función para ocultar el aside
                function hideAside() {
                    asideContainer.classList.add('-translate-x-full');
                    asideContainer.classList.remove('translate-x-0');

                    // Ocultar overlay
                    asideOverlay.classList.add('hidden');

                    // Actualizar iconos
                    iconOpen.classList.remove('hidden');
                    iconClose.classList.add('hidden');

                    // Expandir contenido principal
                    mainContent.classList.remove('col-span-4');
                    mainContent.classList.add('col-span-5');

                    isAsideVisible = false;
                    localStorage.setItem('aside-visible', 'false');
                }

                // Aplicar estado inicial según el ancho de pantalla
                if (window.innerWidth < 768) {
                    // En móvil, siempre empezar oculto
                    isAsideVisible = false;
                    iconOpen.classList.remove('hidden');
                    iconClose.classList.add('hidden');
                } else {
                    // En desktop, usar localStorage
                    if (localStorage.getItem('aside-visible') !== 'false') {
                        showAside();
                    } else {
                        hideAside();
                    }
                }

                // Event listener para el botón toggle
                toggleBtn.addEventListener('click', function() {
                    if (isAsideVisible) {
                        hideAside();
                    } else {
                        showAside();
                    }
                });

                // Cerrar aside al hacer clic en el overlay (móvil)
                asideOverlay.addEventListener('click', function() {
                    hideAside();
                });

                // Ajustar comportamiento en resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 768) {
                        asideOverlay.classList.add('hidden');
                        if (isAsideVisible) {
                            asideContainer.classList.remove('hidden');
                        }
                    }
                });
            });
        </script>
    @endif
</body>

</html>
