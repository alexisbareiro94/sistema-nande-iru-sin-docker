// import axios from 'axios';

import { showToast } from "./toast";

document.addEventListener('DOMContentLoaded', function () {
    const btnAbrirConfig = document.getElementById('btn-abrir-config');
    const btnCerrarConfig = document.getElementById('btn-cerrar-config');
    const btnGuardarConfig = document.getElementById('btn-guardar-config');
    const btnLimpiarConfig = document.getElementById('btn-limpiar-config');
    const modalConfig = document.getElementById('modal-config-numero');
    const inputNumero = document.getElementById('input-numero-factura');
    const indicadorConfig = document.getElementById('indicador-config');
    const numeroConfigurado = document.getElementById('numero-configurado');

    // Verificar si existe configuración al cargar (solo en página index)
    if (btnAbrirConfig) {
        checkConfiguracion();

        // Abrir modal
        btnAbrirConfig.addEventListener('click', function () {
            modalConfig.classList.remove('hidden');
            modalConfig.classList.add('flex');
            inputNumero.focus();
        });

        // Cerrar modal
        btnCerrarConfig?.addEventListener('click', cerrarModal);
        modalConfig?.addEventListener('click', function (e) {
            if (e.target === modalConfig) cerrarModal();
        });
    }

    function cerrarModal() {
        modalConfig.classList.add('hidden');
        modalConfig.classList.remove('flex');
        inputNumero.value = '';
    }

    // Guardar configuración
    if (btnGuardarConfig) {
        btnGuardarConfig.addEventListener('click', async function () {
            const numero = inputNumero.value;
            if (!numero || numero < 1) {
                alert('Por favor ingrese un número válido');
                return;
            }

            try {
                const response = await fetch('/facturas/config/numero', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    body: JSON.stringify({
                        numero: parseInt(numero)
                    })
                });

                const data = await response.json();
                if (data.success) {
                    mostrarIndicador(data.numero);
                    cerrarModal();
                } else {
                    alert('Error al guardar la configuración');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al guardar la configuración');
            }
        });
    }

    // Limpiar configuración
    if (btnLimpiarConfig) {
        btnLimpiarConfig.addEventListener('click', async function () {
            if (!confirm('¿Estás seguro de eliminar la configuración del número de factura?'))
                return;

            try {
                const response = await fetch('/facturas/config/numero', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .content
                    }
                });

                const data = await response.json();
                if (data.success) {
                    ocultarIndicador();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }

    // Verificar configuración existente
    async function checkConfiguracion() {
        try {
            const response = await fetch('/facturas/config/numero');
            const data = await response.json();
            if (data.existe) {
                mostrarIndicador(data.numero);
            }
        } catch (error) {
            console.error('Error al verificar configuración:', error);
        }
    }

    function mostrarIndicador(numero) {
        indicadorConfig.classList.remove('hidden');
        indicadorConfig.classList.add('flex');
        numeroConfigurado.textContent = numero;
    }

    function ocultarIndicador() {
        indicadorConfig.classList.add('hidden');
        indicadorConfig.classList.remove('flex');
        numeroConfigurado.textContent = '---';
    }

    // ==========================================
    // CONFIGURACIÓN DE TIMBRADO
    // ==========================================
    const btnAbrirTimbrado = document.getElementById('btn-abrir-timbrado');
    const btnCerrarTimbrado = document.getElementById('btn-cerrar-timbrado');
    const btnGuardarTimbrado = document.getElementById('btn-guardar-timbrado');
    const btnLimpiarTimbrado = document.getElementById('btn-limpiar-timbrado');
    const modalTimbrado = document.getElementById('modal-config-timbrado');
    const inputTimbrado = document.getElementById('input-timbrado');
    const indicadorTimbrado = document.getElementById('indicador-timbrado');
    const timbradoConfigurado = document.getElementById('timbrado-configurado');

    // Verificar si existe timbrado al cargar
    checkTimbrado();

    // Abrir modal timbrado
    if (btnAbrirTimbrado) {
        btnAbrirTimbrado.addEventListener('click', function () {
            modalTimbrado.classList.remove('hidden');
            modalTimbrado.classList.add('flex');
            inputTimbrado.focus();
        });
    }

    // Cerrar modal timbrado
    if (btnCerrarTimbrado) {
        btnCerrarTimbrado.addEventListener('click', cerrarModalTimbrado);
    }
    if (modalTimbrado) {
        modalTimbrado.addEventListener('click', function (e) {
            if (e.target === modalTimbrado) cerrarModalTimbrado();
        });
    }

    function cerrarModalTimbrado() {
        modalTimbrado.classList.add('hidden');
        modalTimbrado.classList.remove('flex');
        inputTimbrado.value = '';
    }

    // Guardar timbrado
    if (btnGuardarTimbrado) {
        btnGuardarTimbrado.addEventListener('click', async function () {
            const timbrado = inputTimbrado.value;
            if (!timbrado || timbrado < 1) {
                alert('Por favor ingrese un timbrado válido');
                return;
            }

            try {
                const response = await fetch('/facturas/config/timbrado', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        timbrado: parseInt(timbrado)
                    })
                });

                const data = await response.json();
                if (data.success) {
                    mostrarIndicadorTimbrado(data.timbrado);
                    cerrarModalTimbrado();
                } else {
                    alert('Error al guardar el timbrado');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al guardar el timbrado');
            }
        });
    }

    // Limpiar timbrado
    if (btnLimpiarTimbrado) {
        btnLimpiarTimbrado.addEventListener('click', async function () {
            if (!confirm('¿Estás seguro de eliminar la configuración del timbrado?'))
                return;

            try {
                const response = await fetch('/facturas/config/timbrado', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                if (data.success) {
                    ocultarIndicadorTimbrado();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }

    // Verificar timbrado existente
    async function checkTimbrado() {
        try {
            const response = await fetch('/facturas/config/timbrado');
            const data = await response.json();
            if (data.existe) {
                mostrarIndicadorTimbrado(data.timbrado);
            }
        } catch (error) {
            console.error('Error al verificar timbrado:', error);
        }
    }

    function mostrarIndicadorTimbrado(timbrado) {
        if (indicadorTimbrado) {
            indicadorTimbrado.classList.remove('hidden');
            indicadorTimbrado.classList.add('flex');
            timbradoConfigurado.textContent = timbrado;
        }
    }

    function ocultarIndicadorTimbrado() {
        if (indicadorTimbrado) {
            indicadorTimbrado.classList.add('hidden');
            indicadorTimbrado.classList.remove('flex');
            timbradoConfigurado.textContent = '---';
        }
    }



    // Función para obtener el badge según el tipo de foto
    function getTipoBadge(tipo) {
        const badges = {
            'factura': '<span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Factura</span>',
            'comprobante': '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Comprobante</span>',
            'otro': '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Otro</span>'
        };
        return badges[tipo] || '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Desconocido</span>';
    }

    // Función para mostrar skeleton loaders
    function showSkeletons(container, count = 3) {
        container.innerHTML = '';
        for (let i = 0; i < count; i++) {
            const skeleton = document.createElement('div');
            skeleton.className = 'skeleton-item relative rounded-xl overflow-hidden shadow-md animate-pulse';
            skeleton.innerHTML = `
                <div class="w-full h-48 bg-gray-200"></div>
                <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-gray-300 to-transparent">
                    <div class="flex items-center justify-between">
                        <div class="space-y-2">
                            <div class="h-5 w-20 bg-gray-300 rounded-full"></div>
                            <div class="h-4 w-32 bg-gray-300 rounded"></div>
                        </div>
                        <div class="h-6 w-6 bg-gray-300 rounded"></div>
                    </div>
                </div>
            `;
            container.appendChild(skeleton);
        }
    }

    // Función para renderizar las fotos
    const renderImage = async () => {
        const galeriaFotos = document.getElementById('galeria-fotos-factura');
        if (!galeriaFotos) return;

        const facturaId = window.location.pathname.split('/').pop();

        // Mostrar skeletons mientras carga
        showSkeletons(galeriaFotos, 3);

        try {
            const res = await fetch(`/api/facturas/${facturaId}`);
            const data = await res.json();

            galeriaFotos.innerHTML = '';

            if (data.length === 0) {
                // Estado vacío
                galeriaFotos.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-500">No hay fotos aún</p>
                        <p class="text-gray-400 text-sm">Sube fotos de la factura física para documentarla</p>
                    </div>
                `;
                return;
            }

            // Renderizar cada foto
            data.forEach(foto => {
                const fotoDiv = document.createElement('div');
                fotoDiv.id = 'div-foto';
                fotoDiv.dataset.fotoId = foto.id;
                fotoDiv.dataset.fotoUrl = foto.url;
                fotoDiv.className = 'foto-item relative group rounded-xl overflow-hidden shadow-md cursor-pointer';

                fotoDiv.innerHTML = `
                    <img src="${foto.url}" alt="${foto.descripcion || ''}"
                        class="w-full h-48 object-cover">
                    <div class="foto-overlay absolute inset-0 bg-black/50 md:opacity-0 group-hover:opacity-100 transition-opacity">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    ${getTipoBadge(foto.tipo)}
                                    ${foto.descripcion ? `<p class="text-white text-xs mt-1">${foto.descripcion}</p>` : ''}
                                </div>
                                <button class="btn-eliminar-foto-factura text-red-400 hover:text-red-300 transition-colors"
                                    data-foto-id="${foto.id}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                // Click para abrir imagen en nueva pestaña
                fotoDiv.addEventListener('click', function (e) {
                    // No abrir si se hizo click en el botón de eliminar
                    if (e.target.closest('.btn-eliminar-foto-factura')) return;
                    window.open(this.dataset.fotoUrl, '_blank');
                });

                galeriaFotos.appendChild(fotoDiv);
            });

            // Re-adjuntar event listeners para eliminar
            attachDeleteListeners();
        } catch (error) {
            console.error('Error al cargar fotos:', error);
            galeriaFotos.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <p class="text-red-500">Error al cargar las fotos</p>
                </div>
            `;
        }
    };

    // Función para adjuntar listeners de eliminar
    function attachDeleteListeners() {
        document.querySelectorAll('.btn-eliminar-foto-factura').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();

                if (!confirm('¿Estás seguro de eliminar esta foto?')) {
                    return;
                }

                const fotoId = this.dataset.fotoId;

                fetch(`/api/factura/foto/${fotoId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Re-renderizar las fotos
                            renderImage();
                        } else {
                            alert(data.message || 'Error al eliminar la foto');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar la foto');
                    });
            });
        });
    }

    renderImage();

    // Variables para cámara
    let streamCamara = null;

    // Elementos del DOM
    const inputFoto = document.getElementById('input-foto-factura');
    const previewContainer = document.getElementById('preview-container-factura');
    const previewImagen = document.getElementById('preview-imagen-factura');
    const previewNombre = document.getElementById('preview-nombre-factura');
    const previewTamano = document.getElementById('preview-tamano-factura');
    const btnQuitarPreview = document.getElementById('btn-quitar-preview-factura');
    const btnAbrirCamara = document.getElementById('btn-abrir-camara-factura');
    const modalCamara = document.getElementById('modal-camara-factura');
    const videoCamara = document.getElementById('video-camara-factura');
    const canvasCamara = document.getElementById('canvas-camara-factura');
    const btnCapturar = document.getElementById('btn-capturar-foto-factura');
    const btnCerrarCamara = document.getElementById('btn-cerrar-camara-factura');

    // Función para formatear tamaño de archivo
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Preview de imagen al seleccionar archivo
    if (inputFoto) {
        inputFoto.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImagen.src = e.target.result;
                    if (previewNombre) previewNombre.textContent = file.name;
                    if (previewTamano) previewTamano.textContent = formatFileSize(file.size);
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Quitar preview
    if (btnQuitarPreview) {
        btnQuitarPreview.addEventListener('click', function () {
            inputFoto.value = '';
            previewImagen.src = '';
            if (previewNombre) previewNombre.textContent = '';
            if (previewTamano) previewTamano.textContent = '';
            previewContainer.classList.add('hidden');
        });
    }

    // Abrir cámara
    if (btnAbrirCamara) {
        btnAbrirCamara.addEventListener('click', async function () {
            try {
                streamCamara = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment'
                    }
                });
                videoCamara.srcObject = streamCamara;
                modalCamara.classList.remove('hidden');
                modalCamara.classList.add('flex');
            } catch (error) {
                console.error('Error al acceder a la cámara:', error);
                alert('No se pudo acceder a la cámara. Asegúrese de dar permisos.');
            }
        });
    }

    // Cerrar cámara
    function cerrarCamara() {
        if (streamCamara) {
            streamCamara.getTracks().forEach(track => track.stop());
            streamCamara = null;
        }
        if (videoCamara) videoCamara.srcObject = null;
        if (modalCamara) {
            modalCamara.classList.add('hidden');
            modalCamara.classList.remove('flex');
        }
    }

    if (btnCerrarCamara) {
        btnCerrarCamara.addEventListener('click', cerrarCamara);
    }

    // Capturar foto
    if (btnCapturar) {
        btnCapturar.addEventListener('click', function () {
            canvasCamara.width = videoCamara.videoWidth;
            canvasCamara.height = videoCamara.videoHeight;
            const ctx = canvasCamara.getContext('2d');
            ctx.drawImage(videoCamara, 0, 0);

            canvasCamara.toBlob(function (blob) {
                const file = new File([blob], 'foto_camara.jpg', {
                    type: 'image/jpeg'
                });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                inputFoto.files = dataTransfer.files;

                // Mostrar preview
                previewImagen.src = canvasCamara.toDataURL('image/jpeg');
                if (previewNombre) previewNombre.textContent = 'foto_camara.jpg';
                if (previewTamano) previewTamano.textContent = formatFileSize(blob.size);
                previewContainer.classList.remove('hidden');

                cerrarCamara();
            }, 'image/jpeg', 0.9);
        });
    }

    // Anular factura
    const btnAnular = document.getElementById('btn-anular-factura');
    if (btnAnular) {
        btnAnular.addEventListener('click', function () {
            if (!confirm(
                '¿Estás seguro de anular esta factura? Esta acción no se puede deshacer.')) {
                return;
            }

            const facturaId = this.dataset.id;

            fetch(`/facturas/${facturaId}/anular`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                        .content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Factura anulada correctamente');
                        location.reload();
                    } else {
                        alert(data.message || 'Error al anular la factura');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al anular la factura');
                });
        });
    }

    // Subir foto
    const formSubirFoto = document.getElementById('form-subir-foto-factura');
    if (formSubirFoto) {
        formSubirFoto.addEventListener('submit', async function (e) {
            e.preventDefault();

            const facturaId = this.dataset.id;
            const formData = new FormData(this);
            const btnSubmit = this.querySelector('button[type="submit"]');
            const btnOriginalContent = btnSubmit.innerHTML;

            // Mostrar estado de carga
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `
                <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Guardando...
            `;

            try {
                const response = await fetch(`/facturas/${facturaId}/foto`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Limpiar formulario y preview
                    this.reset();
                    previewContainer?.classList.add('hidden');
                    previewImagen.src = '';
                    // Re-renderizar las fotos desde Drive
                    renderImage();
                } else {
                    alert(data.message || 'Error al subir la foto');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al subir la foto');
            } finally {
                // Restaurar botón
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = btnOriginalContent;
            }
        });
    }
});