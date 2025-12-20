@extends('layouts.app')

@section('titulo', 'Gestión de Caja')
@section('ruta-actual', 'Caja')

@section('contenido')
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Caja</h2>
            <p class="text-slate-500 mt-1">Administración de flujo de efectivo y ventas.</p>
        </div>
        <div @class([
            'px-4 py-2 rounded-lg font-bold border-l-4 text-sm uppercase tracking-wide',
            'bg-rose-50 text-rose-700 border-rose-500' => empty(session('caja')),
            'bg-emerald-50 text-emerald-700 border-emerald-500' => session('caja'),
        ])>
            {{ session('caja') ? 'Caja Abierta' : 'Caja Cerrada' }}
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <div class="lg:col-span-8 space-y-8">

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                            Detalles de la sesión
                        </p>
                        <h3 class="text-lg font-medium text-slate-700">
                            @if (session('caja'))
                                Atendido por: <span
                                    class="text-slate-900 font-bold">{{ session('caja')['user']['name'] ?? 'Usuario' }}</span>
                            @else
                                @if ($caja == null)
                                    Sin registros previos
                                @else
                                    Último cierre por: <span class="text-slate-900 font-bold">{{ $caja->user->name }}</span>
                                @endif
                            @endif
                        </h3>
                        <p class="text-sm text-slate-500 mt-1">
                            @if (session('caja'))
                                Apertura: {{ format_time(session('caja')['fecha_apertura']) }}
                            @else
                                @if ($caja)
                                    Cierre: {{ format_time($caja->fecha_cierre) }}
                                @endif
                            @endif
                        </p>
                    </div>

                    @if (auth()->user()->role === 'admin')
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Saldo en Caja</p>
                            <div id="saldo-caja" class="text-3xl font-bold text-slate-800 font-mono">
                                <span class="inline-flex items-center text-slate-400 text-lg">
                                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                    </svg>
                                    ...
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <button id="ir-a-ventas" {{ !session('caja') ? 'disabled' : '' }}
                    class="h-32 flex flex-col items-center justify-center p-6 bg-slate-800 text-white hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400 transition-colors duration-200 group rounded-xl shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 mb-2 group-hover:scale-110 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    <span class="text-lg font-semibold">Nueva Venta</span>
                    @if (!session('caja'))
                        <span class="text-xs mt-1">(Caja cerrada)</span>
                    @endif
                </button>

                <a href="{{ route('servicio.proceso.index') }}"
                    class="h-32 flex flex-col items-center justify-center p-6 bg-indigo-600 text-white hover:bg-indigo-500 transition-colors duration-200 {{ !session('caja') ? 'pointer-events-none opacity-50 bg-slate-200 text-slate-400' : '' }} rounded-xl shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 mb-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                    </svg>
                    <span class="text-lg font-semibold">Servicios en Proceso</span>
                </a>

                <button id="btn-movimiento" {{ !session('caja') ? 'disabled' : '' }}
                    class="cursor-pointer h-16 md:col-span-2 flex items-center justify-center border-2 border-slate-300 text-slate-600 font-bold hover:border-slate-400 hover:text-slate-800 disabled:opacity-40 disabled:cursor-not-allowed transition-all uppercase tracking-wide text-sm rounded-xl">
                    Registrar Entrada / Salida Manual
                </button>
            </div>

            @if (auth()->user()->role === 'admin')
                <div class="mt-8 border-t border-slate-200 pt-8">
                    <h4 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Administración</h4>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button {{ !session('caja') ? 'id=btn-abrir-caja' : 'id=btn-cerrar-caja' }}
                            @class([
                                'flex-1 py-4 px-6 font-bold text-white transition-colors duration-200 text-center rounded-xl shadow-sm',
                                'bg-emerald-600 hover:bg-emerald-700' => empty(session('caja')),
                                'bg-rose-600 hover:bg-rose-700' => session('caja'),
                            ])>
                            {{ session('caja') ? 'CERRAR CAJA' : 'ABRIR CAJA' }}
                        </button>

                        <a href="{{ route('caja.anteriores') }}"
                            class="flex-1 py-4 px-6 font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 text-center transition-colors duration-200 rounded-xl">
                            Historial de Cajas
                        </a>
                    </div>
                </div>
            @endif

        </div>

        <div class="lg:col-span-4">
            <div class="bg-white h-full border-l border-slate-200 pl-0 lg:pl-8 py-0 lg:py-2 rounded-md">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-slate-800 text-lg pt-6">Actividad Reciente</h3>
                </div>

                <div class="space-y-4 max-h-[800px] overflow-y-auto pr-2 custom-scrollbar">
                    @include('caja.includes.movimientos')
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->role == 'admin')
        <div class="bg-white border border-slate-200 p-6 mt-8">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Métricas</h3>
            @include('caja.graficos.graficos')
        </div>
    @endif

    @include('caja.includes.modal-venta')
    @include('caja.includes.modal-abrir-caja')
    @include('caja.includes.modal-add-clientes')
    @include('caja.venta-completada')
    @include('caja.movimientos-manuales')
    @include('caja.carrar-caja')
    @include('caja.includes.cargando')

@section('js')
    <script src="{{ asset('js/movimiento.js') }}"></script>
@endsection
@endsection
