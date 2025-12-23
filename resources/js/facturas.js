document.addEventListener('DOMContentLoaded', function () {
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
        formSubirFoto.addEventListener('submit', function (e) {
            e.preventDefault();

            const facturaId = this.dataset.id;
            const formData = new FormData(this);

            fetch(`/facturas/${facturaId}/foto`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                        .content
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Error al subir la foto');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al subir la foto');
                });
        });
    }

    // Eliminar foto
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
                    'X-CSRF-TOKEN': document.querySelector(
                        'meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('.foto-item').remove();
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
});