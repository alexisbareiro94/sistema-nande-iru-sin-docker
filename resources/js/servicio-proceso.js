import { showToast } from "./toast.js";
import { csrfToken } from "./csrf-token.js";
import axios from 'axios';

document.addEventListener('DOMContentLoaded', function () {
    // Modal de selección
    const modalSeleccion = document.getElementById('modal-seleccion-servicio');
    const btnNuevoServicio = document.getElementById('btn-nuevo-servicio');
    const btnCerrarModal = document.getElementById('btn-cerrar-modal-seleccion');
    const btnCancelarModal = document.getElementById('btn-cancelar-modal');
    const formNuevoServicio = document.getElementById('form-nuevo-servicio');
    const btnBuscarVehiculo = document.getElementById('btn-buscar-vehiculo');
    const inputBuscarPatente = document.getElementById('input-buscar-patente');

    // Abrir modal
    if (btnNuevoServicio) {
        btnNuevoServicio.addEventListener('click', function () {
            modalSeleccion.classList.remove('hidden');
            modalSeleccion.classList.add('flex');
            inputBuscarPatente?.focus();
        });
    }

    // Cerrar modal
    function cerrarModal() {
        modalSeleccion?.classList.add('hidden');
        modalSeleccion?.classList.remove('flex');
        formNuevoServicio?.reset();
        document.getElementById('resultado-vehiculo')?.classList.add('hidden');
        document.getElementById('vehiculo-encontrado')?.classList.add('hidden');
        document.getElementById('vehiculo-no-encontrado')?.classList.add('hidden');
        document.getElementById('vehiculo_id').value = '';
    }

    btnCerrarModal?.addEventListener('click', cerrarModal);
    btnCancelarModal?.addEventListener('click', cerrarModal);

    // Cerrar modal al hacer clic fuera
    modalSeleccion?.addEventListener('click', function (e) {
        if (e.target === modalSeleccion) {
            cerrarModal();
        }
    });

    // Buscar vehículo
    if (btnBuscarVehiculo) {
        btnBuscarVehiculo.addEventListener('click', buscarVehiculo);
    }

    if (inputBuscarPatente) {
        inputBuscarPatente.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarVehiculo();
            }
        });
    }

    async function buscarVehiculo() {
        const patente = inputBuscarPatente?.value.trim();
        if (!patente) {
            showToast('Ingresa una patente para buscar', 'error');
            return;
        }

        const resultadoDiv = document.getElementById('resultado-vehiculo');
        const encontradoDiv = document.getElementById('vehiculo-encontrado');
        const noEncontradoDiv = document.getElementById('vehiculo-no-encontrado');

        try {
            const response = await fetch(`/api/servicio-proceso/buscar-vehiculo?patente=${encodeURIComponent(patente)}`);
            const data = await response.json();

            resultadoDiv.classList.remove('hidden');

            if (data.success && data.exists) {
                encontradoDiv.classList.remove('hidden');
                noEncontradoDiv.classList.add('hidden');

                document.getElementById('vehiculo-info').textContent =
                    `${data.vehiculo.patente} - ${data.vehiculo.marca} ${data.vehiculo.modelo}`;
                document.getElementById('cliente-info').textContent =
                    data.vehiculo.cliente ? `Cliente: ${data.vehiculo.cliente.name}` : 'Sin cliente asignado';
                document.getElementById('vehiculo_id').value = data.vehiculo.id;
            } else {
                encontradoDiv.classList.add('hidden');
                noEncontradoDiv.classList.remove('hidden');
                document.getElementById('vehiculo_id').value = '';
            }
        } catch (error) {
            console.error('Error buscando vehículo:', error);
            showToast('Error al buscar el vehículo', 'error');
        }
    }

    // Crear nuevo servicio
    if (formNuevoServicio) {
        formNuevoServicio.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(formNuevoServicio);
            const data = Object.fromEntries(formData.entries());
            console.log(data);
            // return;

            try {
                const response = await fetch('/api/servicio-proceso', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();                
                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                console.error('Error creando servicio:', error);
                showToast('Error al crear el servicio', 'error');
            }
        });
    }

    // === Vista de detalle del servicio ===

    // Cambiar estado del servicio
    const selectEstado = document.getElementById('select-estado-servicio');
    if (selectEstado) {
        selectEstado.addEventListener('change', async function () {
            const servicioId = this.dataset.id;
            const nuevoEstado = this.value;
            
            try {
                const response = await fetch(`/api/servicio-proceso/${servicioId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ estado: nuevoEstado })
                });

                const result = await response.json();

                if (result.success) {
                    location.reload();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (error) {
                console.error('Error actualizando estado:', error);
                showToast('Error al actualizar el estado', 'error');
            }
        });
    }

    // Cambiar mecánico
    const selectMecanico = document.getElementById('select-mecanico-servicio');
    if (selectMecanico) {
        selectMecanico.addEventListener('change', async function () {
            const servicioId = this.dataset.id;
            const mecanicoId = this.value || null;

            try {
                const response = await fetch(`/api/servicio-proceso/${servicioId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ mecanico_id: mecanicoId })
                });

                const result = await response.json();

                if (result.success) {
                    location.reload();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (error) {
                console.error('Error actualizando mecánico:', error);
                showToast('Error al actualizar el mecánico', 'error');
            }
        });
    }

    // Guardar observaciones
    const btnGuardarObservaciones = document.getElementById('btn-guardar-observaciones');
    if (btnGuardarObservaciones) {
        btnGuardarObservaciones.addEventListener('click', async function () {
            const textarea = document.getElementById('textarea-observaciones');
            const servicioId = textarea.dataset.id;
            const observaciones = textarea.value;

            try {
                const response = await fetch(`/api/servicio-proceso/${servicioId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ observaciones })
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Observaciones guardadas correctamente', 'success');
                } else {
                    showToast(result.error, 'error');
                }
            } catch (error) {
                console.error('Error guardando observaciones:', error);
                showToast('Error al guardar las observaciones', 'error');
            }
        });
    }

    // Preview de foto antes de subir
    const inputFoto = document.getElementById('input-foto-servicio');
    const previewContainer = document.getElementById('preview-container');
    const previewImagen = document.getElementById('preview-imagen');
    const previewNombre = document.getElementById('preview-nombre');
    const previewTamano = document.getElementById('preview-tamano');
    const btnCancelarPreview = document.getElementById('btn-cancelar-preview');

    if (inputFoto) {
        inputFoto.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validar que sea una imagen
                if (!file.type.startsWith('image/')) {
                    showToast('Por favor selecciona un archivo de imagen válido.', 'error');
                    inputFoto.value = '';
                    return;
                }
                
                // Crear preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImagen.src = event.target.result;
                    previewNombre.textContent = file.name;
                    previewTamano.textContent = formatFileSize(file.size);
                    previewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('hidden');
            }
        });
    }

    // Cancelar preview / limpiar selección
    if (btnCancelarPreview) {
        btnCancelarPreview.addEventListener('click', function() {
            inputFoto.value = '';
            previewContainer.classList.add('hidden');
            previewImagen.src = '';
        });
    }

    // Función para formatear el tamaño del archivo
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Subir foto
    const formSubirFoto = document.getElementById('form-subir-foto');
    if (formSubirFoto) {
        formSubirFoto.addEventListener('submit', async function (e) {
            e.preventDefault();

            const servicioId = this.dataset.id;
            const formData = new FormData(this);

            try {
                const response = await fetch(`/api/servicio-proceso/${servicioId}/foto`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    location.reload();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (error) {
                console.error('Error subiendo foto:', error);
                showToast('Error al subir la foto', 'error');
            }
        });
    }

    // Eliminar foto
    document.querySelectorAll('.btn-eliminar-foto').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!confirm('¿Estás seguro de eliminar esta foto?')) return;

            const fotoId = this.dataset.fotoId;

            try {
                const response = await fetch(`/api/servicio-proceso/foto/${fotoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.closest('.foto-item')?.remove();

                    // Si no quedan fotos, mostrar mensaje vacío
                    const galeria = document.getElementById('galeria-fotos');
                    if (galeria && galeria.querySelectorAll('.foto-item').length === 0) {
                        galeria.innerHTML = `
                            <div class="col-span-full text-center py-12">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-gray-500">No hay fotos aún</p>
                                <p class="text-gray-400 text-sm">Sube fotos del ingreso del vehículo para documentar su estado</p>
                            </div>
                        `;
                    }
                } else {
                    showToast(result.error, 'error');
                }
            } catch (error) {
                console.error('Error eliminando foto:', error);
                showToast('Error al eliminar la foto', 'error');
            }
        });
    });

    // === Modales de creación ===

    // Función general para abrir/cerrar modales
    function abrirModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function cerrarModalGeneral(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.querySelector('form')?.reset();
        }
    }

    // Botones para abrir modales de creación
    document.getElementById('btn-abrir-modal-vehiculo')?.addEventListener('click', () => abrirModal('modal-crear-vehiculo'));
    document.getElementById('btn-abrir-modal-cliente')?.addEventListener('click', () => abrirModal('modal-crear-cliente'));
    document.getElementById('btn-abrir-modal-mecanico')?.addEventListener('click', () => abrirModal('modal-crear-mecanico'));

    // Botones para cerrar modales
    document.querySelectorAll('.btn-cerrar-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            const modalId = this.dataset.modal;
            cerrarModalGeneral(modalId);
        });
    });

    // Cerrar modales al hacer clic fuera
    ['modal-crear-vehiculo', 'modal-crear-cliente', 'modal-crear-mecanico'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        modal?.addEventListener('click', function (e) {
            if (e.target === modal) {
                cerrarModalGeneral(modalId);
            }
        });
    });

    // Formulario crear vehículo
    const formCrearVehiculo = document.getElementById('form-crear-vehiculo');
    if (formCrearVehiculo) {
        formCrearVehiculo.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('/api/servicio-proceso/crear-vehiculo', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    // Agregar al select y seleccionar
                    const select = document.getElementById('vehiculo_id');
                    if (select) {
                        const option = document.createElement('option');
                        option.value = result.vehiculo.id;
                        option.textContent = `${result.vehiculo.marca} ${result.vehiculo.modelo} ${result.vehiculo.anio || ''} | ${result.vehiculo.patente}`;
                        option.selected = true;
                        select.appendChild(option);

                        // Actualizar el servicio con el vehículo
                        const servicioId = select.dataset.servicioId;
                        if (servicioId) {
                            await fetch(`/api/servicio-proceso/${servicioId}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                                },
                                body: JSON.stringify({ vehiculo_id: result.vehiculo.id })
                            });
                        }
                    }
                    cerrarModalGeneral('modal-crear-vehiculo');
                    location.reload();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (error) {
                console.error('Error creando vehículo:', error);
                showToast('Error al crear el vehículo', 'error');
            }
        });
    }

    // Formulario crear cliente
    const formCrearCliente = document.getElementById('form-crear-cliente');
    if (formCrearCliente) {
        formCrearCliente.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('/api/servicio-proceso/crear-cliente', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    // Agregar al select y seleccionar
                    const select = document.getElementById('cliente_id');
                    if (select) {
                        const option = document.createElement('option');
                        option.value = result.cliente.id;
                        option.textContent = result.cliente.razon_social || result.cliente.name;
                        option.selected = true;
                        select.appendChild(option);

                        // Actualizar el servicio con el cliente
                        const servicioId = select.dataset.servicioId;
                        if (servicioId) {
                            await fetch(`/api/servicio-proceso/${servicioId}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                                },
                                body: JSON.stringify({ cliente_id: result.cliente.id })
                            });
                        }
                    }
                    cerrarModalGeneral('modal-crear-cliente');
                    location.reload();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (error) {
                console.error('Error creando cliente:', error);
                showToast('Error al crear el cliente', 'error');
            }
        });
    }

    // Formulario crear mecánico
    const formCrearMecanico = document.getElementById('form-crear-mecanico');
    if (formCrearMecanico) {
        formCrearMecanico.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('/api/servicio-proceso/crear-mecanico', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    // Agregar al select y seleccionar
                    const select = document.getElementById('select-mecanico-servicio');
                    if (select) {
                        const option = document.createElement('option');
                        option.value = result.mecanico.id;
                        option.textContent = result.mecanico.name;
                        option.selected = true;
                        select.appendChild(option);

                        // Actualizar el servicio con el mecánico
                        const servicioId = select.dataset.id;
                        if (servicioId) {
                            await fetch(`/api/servicio-proceso/${servicioId}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                                },
                                body: JSON.stringify({ mecanico_id: result.mecanico.id })
                            });
                        }
                    }
                    cerrarModalGeneral('modal-crear-mecanico');
                    location.reload();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (error) {
                console.error('Error creando mecánico:', error);
                showToast('Error al crear el mecánico', 'error');
            }
        });
    }

    // Cambiar vehículo del servicio
    const selectVehiculo = document.getElementById('vehiculo_id');
    if (selectVehiculo && selectVehiculo.dataset.servicioId) {
        selectVehiculo.addEventListener('change', async function () {
            const servicioId = this.dataset.servicioId;
            const vehiculoId = this.value || null;

            try {
                const response = await fetch(`/api/servicio-proceso/${servicioId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ vehiculo_id: vehiculoId })
                });

                const result = await response.json();
                if (result.success) {
                    location.reload();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (error) {
                console.error('Error actualizando vehículo:', error);
            }
        });
    }

    // Cambiar cliente del servicio
    const selectCliente = document.getElementById('cliente_id');
    if (selectCliente && selectCliente.dataset.servicioId) {
        selectCliente.addEventListener('change', async function () {
            const servicioId = this.dataset.servicioId;
            const clienteId = this.value || null;

            try {
                const response = await fetch(`/api/servicio-proceso/${servicioId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    },
                    body: JSON.stringify({ cliente_id: clienteId })
                });                

                const result = await response.json();                
                if (result.success) {
                    location.reload();
                } else {
                    showToast(result.error, 'error');
                }
            } catch (error) {
                console.error('Error actualizando cliente:', error);
            }
        });
    }
});

//procesar venta
if(document.getElementById('btn-procesar-cobro')){
    document.getElementById('btn-procesar-cobro').addEventListener('click', async e => {
        e.preventDefault();
        const modalVentas = document.getElementById('modal-ventas');
        const data = e.target.dataset.servicio;
        const servicioCodigo = document.getElementById('servicio-codigo');
        const divProcesarCobro = document.getElementById('div-procesar-cobro');    
        const servicio = JSON.parse(data);
        if(servicio){
            divProcesarCobro.classList.remove('hidden');
            if(servicio.vehiculo){
                servicioCodigo.textContent = servicio.codigo + ' | ' + servicio.vehiculo?.marca + ' ' + servicio.vehiculo?.modelo + ' | ' + servicio.vehiculo?.patente;
                
                // Guardar datos del vehículo en sessionStorage para pre-llenar en modal de confirmación
                sessionStorage.setItem('servicioVehiculo', JSON.stringify({
                    id: servicio.vehiculo.id,
                    patente: servicio.vehiculo.patente,
                    marca: servicio.vehiculo.marca,
                    modelo: servicio.vehiculo.modelo,
                    anio: servicio.vehiculo.anio || '',
                    mecanico_id: servicio.vehiculo.mecanico_id || servicio.mecanico_id || null
                }));
            }else{
                servicioCodigo.textContent = servicio.codigo;
                sessionStorage.removeItem('servicioVehiculo');
            }
            
            // Guardar datos del cliente si existe
            if(servicio.cliente){
                sessionStorage.setItem('servicioCliente', JSON.stringify({
                    id: servicio.cliente.id,
                    razon_social: servicio.cliente.razon_social || servicio.cliente.name,
                    ruc_ci: servicio.cliente.ruc_ci || ''
                }));
            }
            
            // Guardar que viene de un servicio
            sessionStorage.setItem('fromServicio', 'true');
            sessionStorage.setItem('servicioId', servicio.id);
            
            modalVentas.classList.remove('hidden');
            sessionStorage.setItem('vehiculo', 'true');
        }
    });
}

if(document.getElementById('cerrar-modal-ventas')){
    document.getElementById('cerrar-modal-ventas').addEventListener('click', e => {
        e.preventDefault();
        const modalVentas = document.getElementById('modal-ventas');
        modalVentas.classList.add('hidden');
    });
}

if(sessionStorage.getItem('vehiculo')){
    sessionStorage.removeItem('vehiculo')
    window.location.reload();
}

if(document.getElementById('ver-foto')){
   const btns = document.querySelectorAll('#ver-foto');
   const modalFoto = document.getElementById('modal-foto');
   const imgModalFoto = document.getElementById('img-modal-foto');
   const contenedorFoto = document.getElementById('contenedor-modal-foto');
   const zoomIndicator = document.getElementById('zoom-indicator');
   const zoomInstructions = document.getElementById('zoom-instructions');
   const btnResetZoom = document.getElementById('btn-reset-zoom');
   
   // Estado del zoom
   let scale = 1;
   let translateX = 0;
   let translateY = 0;
   let isDragging = false;
   let startX = 0;
   let startY = 0;
   let lastTranslateX = 0;
   let lastTranslateY = 0;
   
   // Constantes
   const MIN_SCALE = 1;
   const MAX_SCALE = 5;
   const ZOOM_SENSITIVITY = 0.002;
   
   // Función para actualizar la transformación
   function updateTransform() {
       imgModalFoto.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
       
       // Actualizar indicador
       const percentage = Math.round(scale * 100);
       zoomIndicator.textContent = percentage + '%';
       zoomIndicator.style.opacity = scale > 1 ? '1' : '0';
       
       // Mostrar/ocultar botón reset
       if (scale > 1) {
           btnResetZoom.classList.remove('hidden');
           zoomInstructions.style.opacity = '0';
           imgModalFoto.style.cursor = isDragging ? 'grabbing' : 'grab';
       } else {
           btnResetZoom.classList.add('hidden');
           zoomInstructions.style.opacity = '1';
           imgModalFoto.style.cursor = 'zoom-in';
       }
   }
   
   // Función para resetear zoom
   function resetZoom() {
       scale = 1;
       translateX = 0;
       translateY = 0;
       imgModalFoto.style.transition = 'transform 0.3s ease-out';
       updateTransform();
       setTimeout(() => {
           imgModalFoto.style.transition = 'transform 0.1s ease-out';
       }, 300);
   }
   
   // Función para limitar el desplazamiento
   function clampTranslation() {
       if (scale <= 1) {
           translateX = 0;
           translateY = 0;
           return;
       }
       
       const rect = imgModalFoto.getBoundingClientRect();
       const containerRect = contenedorFoto.getBoundingClientRect();
       
       const maxTranslateX = Math.max(0, (rect.width - containerRect.width) / 2);
       const maxTranslateY = Math.max(0, (rect.height - containerRect.height) / 2);
       
       translateX = Math.max(-maxTranslateX, Math.min(maxTranslateX, translateX));
       translateY = Math.max(-maxTranslateY, Math.min(maxTranslateY, translateY));
   }
   
   // Zoom con rueda del ratón (desktop)
   contenedorFoto.addEventListener('wheel', e => {
       e.preventDefault();
       
       const delta = -e.deltaY * ZOOM_SENSITIVITY;
       const newScale = Math.max(MIN_SCALE, Math.min(MAX_SCALE, scale + delta * scale));
       
       if (newScale !== scale) {
           // Zoom hacia el cursor
           const rect = imgModalFoto.getBoundingClientRect();
           const centerX = rect.left + rect.width / 2;
           const centerY = rect.top + rect.height / 2;
           
           const mouseX = e.clientX - centerX;
           const mouseY = e.clientY - centerY;
           
           const scaleRatio = newScale / scale;
           
           translateX = mouseX - (mouseX - translateX) * scaleRatio;
           translateY = mouseY - (mouseY - translateY) * scaleRatio;
           
           scale = newScale;
           clampTranslation();
           updateTransform();
       }
   }, { passive: false });
   
   // Variables para pinch-to-zoom (móvil)
   let initialDistance = 0;
   let initialScale = 1;
   let initialMidpoint = { x: 0, y: 0 };
   
   function getDistance(touch1, touch2) {
       return Math.hypot(touch2.clientX - touch1.clientX, touch2.clientY - touch1.clientY);
   }
   
   function getMidpoint(touch1, touch2) {
       return {
           x: (touch1.clientX + touch2.clientX) / 2,
           y: (touch1.clientY + touch2.clientY) / 2
       };
   }
   
   // Touch start
   imgModalFoto.addEventListener('touchstart', e => {
       if (e.touches.length === 2) {
           e.preventDefault();
           initialDistance = getDistance(e.touches[0], e.touches[1]);
           initialScale = scale;
           initialMidpoint = getMidpoint(e.touches[0], e.touches[1]);
           lastTranslateX = translateX;
           lastTranslateY = translateY;
       } else if (e.touches.length === 1 && scale > 1) {
           isDragging = true;
           startX = e.touches[0].clientX - translateX;
           startY = e.touches[0].clientY - translateY;
           updateTransform();
       }
   }, { passive: false });
   
   // Touch move
   imgModalFoto.addEventListener('touchmove', e => {
       if (e.touches.length === 2) {
           e.preventDefault();
           const currentDistance = getDistance(e.touches[0], e.touches[1]);
           const currentMidpoint = getMidpoint(e.touches[0], e.touches[1]);
           
           // Calcular nuevo scale
           const newScale = Math.max(MIN_SCALE, Math.min(MAX_SCALE, initialScale * (currentDistance / initialDistance)));
           
           // Calcular movimiento del midpoint
           const midpointDeltaX = currentMidpoint.x - initialMidpoint.x;
           const midpointDeltaY = currentMidpoint.y - initialMidpoint.y;
           
           scale = newScale;
           translateX = lastTranslateX + midpointDeltaX;
           translateY = lastTranslateY + midpointDeltaY;
           
           clampTranslation();
           updateTransform();
       } else if (e.touches.length === 1 && isDragging && scale > 1) {
           e.preventDefault();
           translateX = e.touches[0].clientX - startX;
           translateY = e.touches[0].clientY - startY;
           clampTranslation();
           updateTransform();
       }
   }, { passive: false });
   
   // Touch end
   imgModalFoto.addEventListener('touchend', e => {
       if (e.touches.length < 2) {
           initialDistance = 0;
       }
       if (e.touches.length === 0) {
           isDragging = false;
           updateTransform();
       }
   });
   
   // Arrastrar con ratón (desktop)
   imgModalFoto.addEventListener('mousedown', e => {
       if (scale > 1) {
           e.preventDefault();
           isDragging = true;
           startX = e.clientX - translateX;
           startY = e.clientY - translateY;
           updateTransform();
       }
   });
   
   document.addEventListener('mousemove', e => {
       if (isDragging && scale > 1) {
           translateX = e.clientX - startX;
           translateY = e.clientY - startY;
           clampTranslation();
           updateTransform();
       }
   });
   
   document.addEventListener('mouseup', () => {
       isDragging = false;
       updateTransform();
   });
   
   // Doble clic para zoom rápido
   imgModalFoto.addEventListener('dblclick', e => {
       e.preventDefault();
       if (scale > 1) {
           resetZoom();
       } else {
           // Zoom 2x hacia el punto del clic
           const rect = imgModalFoto.getBoundingClientRect();
           const centerX = rect.left + rect.width / 2;
           const centerY = rect.top + rect.height / 2;
           
           const mouseX = e.clientX - centerX;
           const mouseY = e.clientY - centerY;
           
           scale = 2;
           translateX = -mouseX;
           translateY = -mouseY;
           
           clampTranslation();
           imgModalFoto.style.transition = 'transform 0.3s ease-out';
           updateTransform();
           setTimeout(() => {
               imgModalFoto.style.transition = 'transform 0.1s ease-out';
           }, 300);
       }
   });
   
   // Botón reset zoom
   btnResetZoom?.addEventListener('click', e => {
       e.preventDefault();
       e.stopPropagation();
       resetZoom();
   });
   
   // Abrir modal
   btns.forEach(btn => {
       btn.addEventListener('click', e => {
           e.preventDefault();           
           const rutaFoto = e.target.dataset.ruta;
           
           resetZoom();
           imgModalFoto.src = rutaFoto;
           modalFoto.classList.remove('hidden');
           zoomInstructions.style.opacity = '1';
       });
   });

   // Cerrar modal
   document.getElementById('btn-cerrar-modal-foto').addEventListener('click', e => {
       e.preventDefault();
       resetZoom();
       modalFoto.classList.add('hidden');
   });
   
   // Cerrar con Escape
   document.addEventListener('keydown', e => {
       if (e.key === 'Escape' && !modalFoto.classList.contains('hidden')) {
           resetZoom();
           modalFoto.classList.add('hidden');
       }
   });
   
   // Cerrar al hacer clic fuera de la imagen
   modalFoto.addEventListener('click', e => {
       if (e.target === modalFoto || e.target === contenedorFoto) {
           resetZoom();
           modalFoto.classList.add('hidden');
       }
   });
}

