<!-- Overlay -->
<div id="modal-foto" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/80">

    <!-- Botón cerrar -->
    <button id="btn-cerrar-modal-foto"
        class="absolute top-4 right-4 text-white text-3xl font-light hover:text-gray-300 transition z-10"
        aria-label="Cerrar">
        &times;
    </button>

    <!-- Indicador de zoom -->
    <div id="zoom-indicator"
        class="absolute top-4 left-4 text-white text-sm bg-black/50 px-3 py-1 rounded-full z-10 opacity-0 transition-opacity duration-300">
        100%
    </div>

    <!-- Instrucciones de zoom -->
    <div id="zoom-instructions"
        class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white text-sm bg-black/50 px-4 py-2 rounded-full z-10 pointer-events-none">
        <span class="hidden md:inline">Rueda del ratón para zoom • Arrastra para mover</span>
        <span class="md:hidden">Pellizca para zoom • Arrastra para mover</span>
    </div>

    <!-- Botón reset zoom -->
    <button id="btn-reset-zoom"
        class="hidden absolute bottom-4 right-4 text-white text-sm bg-black/50 px-4 py-2 rounded-full z-10 hover:bg-black/70 transition"
        aria-label="Resetear zoom">
        Resetear zoom
    </button>

    <!-- Contenedor de la imagen -->
    <div id="contenedor-modal-foto" class="w-full h-full flex items-center justify-center overflow-hidden">
        <img id="img-modal-foto" src="" alt="Foto"
            class="max-w-full max-h-full object-contain rounded-lg shadow-2xl select-none"
            style="touch-action: none; transform-origin: center center; transition: transform 0.1s ease-out;"
            draggable="false">
    </div>
</div>
