document.getElementById('procesar-venta').addEventListener('click', () => {
    const carrito = JSON.parse(sessionStorage.getItem('carrito')) ?? {};
    const ruc = document.getElementById('i-ruc-ci');
    const razon = document.getElementById('i-nombre-razon');

    if (Object.entries(carrito).length === 0) {
        showToast('No hay productos en el carrito', 'error');
        return;
    }
    if (ruc.value.trim() == '') {
        ruc.classList.remove('border-gray-300', 'focus:ring-yellow-400', 'focus:border-yellow-400')
        ruc.classList.add('border-red-500', 'ring-2', 'ring-red-500', 'focus:border-red-500', 'bg-red-100');
        ruc.placeholder = 'Campo Obligatorio';
    }
    if (razon.value.trim() == '') {
        razon.classList.remove('border-gray-300', 'focus:ring-yellow-400', 'focus:border-yellow-400')
        razon.classList.add('border-red-500', 'ring-2', 'ring-red-500', 'focus:border-red-500', 'bg-red-100');
        razon.placeholder = 'Campo Obligatorio';
    }
    if (razon.value.trim() != '' && ruc.value.trim() != '') {
        document.getElementById('modal-confirmar-venta').classList.remove('hidden')
        document.getElementById('razon-venta').innerHTML = razon.value.trim();
        document.getElementById('ruc-venta').innerHTML = ruc.value.trim();
        resumenCarrito();
        
        // Pre-llenar datos del vehículo si viene de un servicio
        preLlenarVehiculoServicio();
    }
});

// Función para pre-llenar datos del vehículo cuando viene de un servicio
function preLlenarVehiculoServicio() {
    const fromServicio = sessionStorage.getItem('fromServicio');
    const servicioVehiculo = sessionStorage.getItem('servicioVehiculo');
    
    if (fromServicio === 'true' && servicioVehiculo) {
        const vehiculo = JSON.parse(servicioVehiculo);
        
        // Pre-llenar el input de patente
        const inputPatente = document.getElementById('input-patente');
        if (inputPatente && vehiculo.patente) {
            inputPatente.value = vehiculo.patente;
        }
        
        // Pre-llenar el ID del vehículo
        const vehiculoIdInput = document.getElementById('vehiculo-id-venta');
        if (vehiculoIdInput && vehiculo.id) {
            vehiculoIdInput.value = vehiculo.id;
        }
        
        // Mostrar info del vehículo encontrado
        const infoEncontrado = document.getElementById('info-vehiculo-encontrado');
        const infoTexto = document.getElementById('vehiculo-info-texto');
        const btnNuevoVehiculo = document.getElementById('cont-btn-nuevo-vehiculo');
        
        if (infoEncontrado && infoTexto) {
            infoTexto.textContent = `${vehiculo.marca} ${vehiculo.modelo} ${vehiculo.anio || ''} - ${vehiculo.patente}`;
            infoEncontrado.classList.remove('hidden');
            btnNuevoVehiculo?.classList.add('hidden');
        }
        
        // Pre-seleccionar el mecánico si existe
        if (vehiculo.mecanico_id) {
            const selectMecanico = document.getElementById('select-mecanico-venta');
            if (selectMecanico) {
                selectMecanico.value = vehiculo.mecanico_id;
            }
        }
        
        // Ocultar la sección de vehículo ya que viene pre-llenado
        const datosVehiculo = document.getElementById('datos-vehiculo');
        if (datosVehiculo) {
            // Agregar un indicador visual de que el vehículo está pre-cargado
            const existingBadge = datosVehiculo.querySelector('.servicio-badge');
            if (!existingBadge) {
                const badge = document.createElement('span');
                badge.className = 'servicio-badge ml-2 text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full';
                badge.textContent = '✓ Del servicio';
                datosVehiculo.querySelector('h3')?.appendChild(badge);
            }
        }
    }
}

document.getElementById('confirmar-venta').addEventListener('click', async () => {
    const mixtoEfectivo = document.getElementById('mixto-efectivo') ?? '';
    const mixtoTransf = document.getElementById('mixto-transf') ?? '';
    const montoRecibido = document.getElementById('i-monto-recibido') ?? '';
    let formaPago = {};

    if (efectivo.checked == false && transf.checked == false && mixto.checked == false) {
        document.getElementById('no-radio').classList.remove('hidden');
        return;
    }

    if (montoRecibido != '' && mixtoEfectivo == '' && mixtoTransf == '') {
        if (montoRecibido.value == '') {
            montoRecibido.classList.remove('border-gray-300')
            montoRecibido.classList.add('border-red-500', 'bg-red-100', 'ring-2', 'ring-red-500')
            montoRecibido.placeholder = 'Ingresa el monto recibido';
            return;
        }
    }
    if (montoRecibido == '' && mixtoTransf != '' && mixtoEfectivo != '') {
        if (mixtoEfectivo.value == '') {
            mixtoEfectivo.classList.remove('border-gray-300')
            mixtoEfectivo.classList.add('border-red-500', 'bg-red-100', 'ring-2', 'ring-red-500')
            mixtoEfectivo.placeholder = 'Ingresa el monto recibido';

            if (mixtoTransf.value == '') {
                mixtoTransf.classList.remove('border-gray-300')
                mixtoTransf.classList.add('border-red-500', 'bg-red-100', 'ring-2', 'ring-red-500')
                mixtoTransf.placeholder = 'Ingresa el monto recibido';
                return;
            } else {
                return;
            }
        }
        if (mixtoTransf.value == '') {
            mixtoTransf.classList.remove('border-gray-300')
            mixtoTransf.classList.add('border-red-500', 'bg-red-100', 'ring-2', 'ring-red-500')
            mixtoTransf.placeholder = 'Ingresa el monto recibido';
            return;
        }
    }
    if (efectivo.checked) {
        formaPago = {
            'efectivo': montoRecibido.value.trim(),
        }
    } else if (transf.checked) {
        formaPago = {
            'transferencia': montoRecibido.value.trim(),
        }
    } else {
        formaPago = {
            'mixto': {
                'efectivo': mixtoEfectivo.value.trim(),
                'transferencia': mixtoTransf.value.trim(),
            }
        }
    }
    const totalCart = JSON.parse(sessionStorage.getItem('totalCarrito')) || {};
    let monto = parseInt(montoRecibido.value);
    if (totalCart.total > monto) {
        showToast('El monto recibido es menor al total de la venta', 'error');
        return;
    }
    const data = await confirmarVenta(formaPago, montoRecibido);    
    document.getElementById('modal-carga').classList.remove('hidden');
    setTimeout(() => {
        document.getElementById('modal-carga').classList.add('hidden');
        if(sessionStorage.getItem('vehiculo')){
            sessionStorage.removeItem('vehiculo');
            document.getElementById('modal-ventas').classList.add('hidden');
            document.getElementById('modal-confirmar-venta').classList.add('hidden');
            resumenVenta(data);
        }else{
            resumenVenta(data);
            limpiarUI();
        }
    }, 500);

});


function resumenVenta(data) {    
    console.log(data)
    const modalVentaCompletada = document.getElementById('modal-venta-completada');
    const resumenVenta = document.getElementById('resumen-venta');
    modalVentaCompletada.classList.remove('hidden');
    resumenVenta.innerHTML = '';
    const ul = document.createElement('ul');
    ul.classList.add('space-y-1');
    ul.innerHTML = `
                <li><strong>Código de Venta:</strong>${data.venta.codigo}</li>
                <li>
                    <strong>Cliente:</strong>
                    <ul class="ml-4 list-disc list-inside">
                        <li>Razón social: ${data.venta.cliente.razon_social}</li>
                        <li>RUC o CI: ${data.venta.cliente.ruc_ci}</li>
                    </ul>
                </li>
                <strong>Productos:</strong>
                <li id="li-productos">
                    
                </li>

                <li class="mt-4"><strong>Subtotal:</strong> ${data.venta.subtotal.toLocaleString('es-PY')} Gs</li>
                <li class="font-bold text-gray-900"><strong>Vuelto:</strong> ${(data.venta.monto_recibido - data.venta.total).toLocaleString('es-PY')}  Gs</li>
                <li class="font-bold text-gray-900"><strong>Total:</strong> ${data.venta.total.toLocaleString('es-PY')} Gs</li>
    `;
    resumenVenta.append(ul);

    const li = document.getElementById('li-productos');
    li.innerHTML = '';

    data.productos.forEach(producto => {
        const ulP = document.createElement('ul');
        ulP.classList.add('ml-4', 'list-disc', 'list-inside')
        console.log(producto);
        ulP.innerHTML = `
                    <li>${producto.nombre}</li>                
        `;
        li.appendChild(ulP);
    });

}

function resumenCarrito() {
    const carrito = JSON.parse(sessionStorage.getItem('carrito')) || {};
    const totalResumen = JSON.parse(sessionStorage.getItem('totalCarrito')) || {};
    const bodyTableVenta = document.getElementById('body-tabla-venta');
    const footerTableVenta = document.getElementById('footer-tabla-venta');

    bodyTableVenta.innerHTML = '';
    footerTableVenta.innerHTML = '';
    Object.entries(carrito).forEach(([id, producto]) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                    ${producto.nombre}
                                </th>
                                <td class="px-6 py-4">
                                    ${producto.cantidad}
                                </td>
                                <td class="px-6 py-4">
                                    ${producto.descuento ? producto.precio_descuento : producto.precio}
                                </td>
        `;
        bodyTableVenta.appendChild(tr)
    });

    const trF = document.createElement('tr');
    trF.classList.add('font-semibold', 'text-gray-900', 'bg-gray-200');
    trF.innerHTML = `
                                <th scope="row" class="px-6 py-3 text-base">Total</th>
                                <td class="px-6 py-3">${totalResumen.cantidadTotal}</td>
                                <td id="precio-tabla" class="px-6 py-3">Gs. ${totalResumen.total}</td>
    `;
    footerTableVenta.appendChild(trF);
}

const mixto = document.getElementById('mixto');
const efectivoTransf = document.querySelectorAll('#efectivo, #transf');
const contMontoRecibido = document.getElementById('monto-recibido');

mixto.addEventListener('change', () => {
    contMontoRecibido.innerHTML = `            
                <div class="flex justify-between">
                    <label for="mixto-efectivo" class="flex text-start text-sm text-gray-800 font-semibold mt-1 pr-4 md:pr-12">Efectivo Recibido:</label>
                    <input class=" border border-gray-300 px-3 py-1 rounded-md" type="number" name="mixto-efectivo" id="mixto-efectivo">
                </div>
                <div class="flex justify-between">
                    <label for="mixto-transf" class="text-start text-sm text-gray-800 font-semibold mt-1 pr-0.5 md:pr-12">Monto en Transferencia:</label>
                    <input class=" border border-gray-300 px-3 py-1 rounded-md" type="number" name="mixto-transf" id="mixto-transf">
                </div>
    `
});

efectivoTransf.forEach(btn => {
    btn.addEventListener('change', () => {
        contMontoRecibido.innerHTML = `
                <div class="flex justify-between">
                    <label for="monto-recibido" class="text-sm text-gray-800 font-semibold mt-1 pr-4 md:pr-12">Monto Recibido:</label>
                    <input class="border border-gray-300 px-3 py-1 rounded-md" type="number" name="monto-recibido" id="i-monto-recibido">
                </div>                
    `;
    })
})

let timerVentaC;
async function confirmarVenta(formaPago, montoRecibido) {
    try {
        carrito = JSON.parse(sessionStorage.getItem('carrito')) || {};
        total = JSON.parse(sessionStorage.getItem('totalCarrito')) || {};

        ventaData = new FormData();
        ventaData.append('carrito', JSON.stringify(carrito));
        ventaData.append('total', JSON.stringify(total));
        ventaData.append('forma_pago', JSON.stringify(formaPago));
        ventaData.append('ruc', document.getElementById('i-ruc-ci').value.trim());
        ventaData.append('razon', document.getElementById('i-nombre-razon').value.trim());
        ventaData.append('monto_recibido', montoRecibido.value);

        // Agregar vehículo y mecánico
        const vehiculoId = document.getElementById('vehiculo-id-venta')?.value || '';
        const mecanicoId = document.getElementById('select-mecanico-venta')?.value || '';
        if (vehiculoId) ventaData.append('vehiculo_id', vehiculoId);
        if (mecanicoId) ventaData.append('mecanico_id', mecanicoId);        

        // Agregar ID del servicio si viene de un cobro de servicio
        const servicioId = sessionStorage.getItem('servicioId');
        if (servicioId) ventaData.append('servicio_id', servicioId);
                
        const res = await fetch(`/api/venta`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            body: ventaData,
        });
        const data = await res.json();        
        
        if (!res.ok) {
            throw data;
        }
        document.getElementById('input-b-producto-ventas').value = '';
        showToast('Venta realizada con éxito');        
        return data;
    } catch (err) {
        showToast(`${err.error}`, 'error');
    }

}

async function limpiarUI() {
    //await loadChart();
    const ruc = document.getElementById('i-ruc-ci');
    const razon = document.getElementById('i-nombre-razon');
    sessionStorage.clear();
    renderCarrito();
    document.getElementById('totalCarrito').innerHTML = ''
    document.getElementById('subTotalCarrito').innerHTML = ''
    document.getElementById('form-cliente-venta').reset();
    document.getElementById('modal-ventas').classList.add('hidden');
    document.getElementById('form-monto-recibido').reset();
    ruc.classList.remove('border-red-500', 'ring-2', 'ring-red-500', 'focus:border-red-500', 'bg-red-100');
    ruc.classList.add('border-gray-300', 'focus:ring-yellow-400', 'focus:border-yellow-400')
    ruc.placeholder = 'Ingrese RUC O CI';
    razon.classList.remove('border-red-500', 'ring-2', 'ring-red-500', 'focus:border-red-500', 'bg-red-100');
    razon.classList.add('border-gray-300', 'focus:ring-yellow-400', 'focus:border-yellow-400')
    razon.placeholder = 'Ingrese nombre o razon social';
    document.getElementById('modal-confirmar-venta').classList.add('hidden')
    document.getElementById('form-b-productos-ventas').reset();
    await recargarMovimientos();
    await recargarSaldo();
    document.body.classList.remove('overflow-hidden');
}

document.getElementById('form-monto-recibido').addEventListener('submit', (e) => {
    e.preventDefault();
});

// ========== VEHÍCULOS Y MECÁNICOS ==========
let vehiculoSeleccionado = null;
let mecanicos = [];

// Cargar mecánicos al iniciar
async function cargarMecanicos() {
    try {
        const res = await fetch('/api/users?role=mecanico');
        const data = await res.json();
        mecanicos = data.users || [];        
        const selectMecanico = document.getElementById('select-mecanico-venta');
        const selectMecanicoModal = document.getElementById('nuevo-vehiculo-mecanico');

        if (selectMecanico) {
            selectMecanico.innerHTML = '<option value="">Sin mecánico</option>';
            mecanicos.forEach(m => {
                selectMecanico.innerHTML += `<option value="${m.id}">${m.name}</option>`;
            });
        }

        if (selectMecanicoModal) {
            selectMecanicoModal.innerHTML = '<option value="">Sin mecánico</option>';
            mecanicos.forEach(m => {
                selectMecanicoModal.innerHTML += `<option value="${m.id}">${m.name}</option>`;
            });
        }
    } catch (error) {
        console.error('Error al cargar mecánicos:', error);
    }
}

// Buscar vehículo por patente
async function buscarVehiculoPorPatente() {
    const inputPatente = document.getElementById('input-patente');
    const patente = inputPatente.value.trim().toUpperCase().replace(/\s/g, '');

    if (patente.length < 3) {
        showToast('Ingresa al menos 3 caracteres de la patente', 'warning');
        return;
    }

    try {
        const res = await fetch(`/api/vehiculo/patente?patente=${encodeURIComponent(patente)}`);
        const data = await res.json();

        const infoEncontrado = document.getElementById('info-vehiculo-encontrado');
        const btnNuevoVehiculo = document.getElementById('cont-btn-nuevo-vehiculo');
        const infoTexto = document.getElementById('vehiculo-info-texto');
        const vehiculoIdInput = document.getElementById('vehiculo-id-venta');

        if (data.encontrado && data.vehiculo) {
            vehiculoSeleccionado = data.vehiculo;
            vehiculoIdInput.value = data.vehiculo.id;
            infoTexto.textContent = `${data.vehiculo.marca} ${data.vehiculo.modelo} ${data.vehiculo.anio || ''} - ${data.vehiculo.patente}`;
            infoEncontrado.classList.remove('hidden');
            btnNuevoVehiculo.classList.add('hidden');

            // Si el vehículo tiene mecánico, seleccionarlo
            if (data.vehiculo.mecanico_id) {
                document.getElementById('select-mecanico-venta').value = data.vehiculo.mecanico_id;
            }

            showToast('Vehículo encontrado', 'success');
        } else {
            vehiculoSeleccionado = null;
            vehiculoIdInput.value = '';
            infoEncontrado.classList.add('hidden');
            btnNuevoVehiculo.classList.remove('hidden');
            btnNuevoVehiculo.classList.add('flex');

            // Pre-llenar patente en modal de nuevo vehículo
            document.getElementById('nuevo-vehiculo-patente').value = patente;

            showToast('Vehículo no encontrado. Puedes registrarlo.', 'warning');
        }
    } catch (error) {
        console.error('Error al buscar vehículo:', error);
        showToast('Error al buscar vehículo', 'error');
    }
}

// Event listener para buscar patente
document.getElementById('btn-buscar-patente')?.addEventListener('click', buscarVehiculoPorPatente);

document.getElementById('input-patente')?.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        buscarVehiculoPorPatente();
    }
});

// Abrir modal nuevo vehículo
document.getElementById('btn-nuevo-vehiculo-modal')?.addEventListener('click', () => {
    document.getElementById('modal-nuevo-vehiculo-venta').classList.remove('hidden');
    document.getElementById('modal-nuevo-vehiculo-venta').classList.add('flex');
});

// Cerrar modal nuevo vehículo
document.getElementById('cerrar-modal-nuevo-vehiculo')?.addEventListener('click', cerrarModalNuevoVehiculo);
document.getElementById('cancelar-nuevo-vehiculo')?.addEventListener('click', cerrarModalNuevoVehiculo);

function cerrarModalNuevoVehiculo() {
    document.getElementById('modal-nuevo-vehiculo-venta').classList.add('hidden');
    document.getElementById('modal-nuevo-vehiculo-venta').classList.remove('flex');
}

// Guardar nuevo vehículo
document.getElementById('form-nuevo-vehiculo-venta')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = {
        patente: document.getElementById('nuevo-vehiculo-patente').value.trim().toUpperCase(),
        marca: document.getElementById('nuevo-vehiculo-marca').value.trim(),
        modelo: document.getElementById('nuevo-vehiculo-modelo').value.trim(),
        anio: document.getElementById('nuevo-vehiculo-anio').value || null,
        color: document.getElementById('nuevo-vehiculo-color').value.trim() || null,
        kilometraje: document.getElementById('nuevo-vehiculo-km').value || null,
        mecanico_id: document.getElementById('nuevo-vehiculo-mecanico').value || null,
    };

    try {
        const res = await fetch('/vehiculos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(formData),
        });

        const data = await res.json();

        if (data.success) {
            vehiculoSeleccionado = data.vehiculo;
            document.getElementById('vehiculo-id-venta').value = data.vehiculo.id;
            document.getElementById('vehiculo-info-texto').textContent =
                `${data.vehiculo.marca} ${data.vehiculo.modelo} ${data.vehiculo.anio || ''} - ${data.vehiculo.patente}`;

            document.getElementById('info-vehiculo-encontrado').classList.remove('hidden');
            document.getElementById('cont-btn-nuevo-vehiculo').classList.add('hidden');
            document.getElementById('input-patente').value = data.vehiculo.patente;

            if (data.vehiculo.mecanico_id) {
                document.getElementById('select-mecanico-venta').value = data.vehiculo.mecanico_id;
            }

            cerrarModalNuevoVehiculo();
            document.getElementById('form-nuevo-vehiculo-venta').reset();
            showToast('Vehículo registrado correctamente', 'success');
        } else {
            showToast(data.error || 'Error al guardar vehículo', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error al guardar vehículo', 'error');
    }
});

// Cargar mecánicos cuando se carga la página
document.addEventListener('DOMContentLoaded', () => {
    cargarMecanicos();
});

// Resetear campos de vehículo al limpiar UI
const originalLimpiarUI = limpiarUI;
limpiarUI = async function () {
    await originalLimpiarUI();

    // Limpiar campos de vehículo
    vehiculoSeleccionado = null;
    document.getElementById('input-patente').value = '';
    document.getElementById('vehiculo-id-venta').value = '';
    document.getElementById('info-vehiculo-encontrado')?.classList.add('hidden');
    document.getElementById('cont-btn-nuevo-vehiculo')?.classList.add('hidden');
    document.getElementById('select-mecanico-venta').value = '';
    
    // Limpiar badge de servicio si existe
    const servicioBadge = document.querySelector('.servicio-badge');
    if (servicioBadge) {
        servicioBadge.remove();
    }
    
    // Limpiar datos de servicio del sessionStorage
    sessionStorage.removeItem('fromServicio');
    sessionStorage.removeItem('servicioVehiculo');
    sessionStorage.removeItem('servicioCliente');
    sessionStorage.removeItem('servicioId');
};

// ========== NUEVO MECÁNICO ==========

// Abrir modal nuevo mecánico
document.getElementById('btn-nuevo-mecanico-modal')?.addEventListener('click', () => {
    document.getElementById('modal-nuevo-mecanico-venta').classList.remove('hidden');
    document.getElementById('modal-nuevo-mecanico-venta').classList.add('flex');
});

// Cerrar modal nuevo mecánico
document.getElementById('cerrar-modal-nuevo-mecanico')?.addEventListener('click', cerrarModalNuevoMecanico);
document.getElementById('cancelar-nuevo-mecanico')?.addEventListener('click', cerrarModalNuevoMecanico);

function cerrarModalNuevoMecanico() {
    document.getElementById('modal-nuevo-mecanico-venta').classList.add('hidden');
    document.getElementById('modal-nuevo-mecanico-venta').classList.remove('flex');
}

// Guardar nuevo mecánico
document.getElementById('form-nuevo-mecanico-venta')?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = {
        name: document.getElementById('nuevo-mecanico-nombre').value.trim(),
        razon_social: document.getElementById('nuevo-mecanico-nombre').value.trim(),
        ruc_ci: document.getElementById('nuevo-mecanico-ruc').value.trim() || null,
        telefono: document.getElementById('nuevo-mecanico-telefono').value || null,
        role: 'mecanico',
    };

    if (!formData.name) {
        showToast('El nombre del mecánico es obligatorio', 'error');
        return;
    }

    try {
        const res = await fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(formData),
        });

        const data = await res.json();

        if (data.success && data.cliente) {
            // Agregar al select de mecánicos
            const selectMecanico = document.getElementById('select-mecanico-venta');
            const selectMecanicoModal = document.getElementById('nuevo-vehiculo-mecanico');

            const option = document.createElement('option');
            option.value = data.cliente.id;
            option.textContent = data.cliente.name || data.cliente.razon_social;
            option.selected = true;
            selectMecanico?.appendChild(option);

            // También agregar al select del modal de vehículo
            if (selectMecanicoModal) {
                const optionModal = document.createElement('option');
                optionModal.value = data.cliente.id;
                optionModal.textContent = data.cliente.name || data.cliente.razon_social;
                selectMecanicoModal.appendChild(optionModal);
            }

            // Seleccionar el nuevo mecánico
            if (selectMecanico) selectMecanico.value = data.cliente.id;

            cerrarModalNuevoMecanico();
            document.getElementById('form-nuevo-mecanico-venta').reset();
            showToast('Mecánico registrado correctamente', 'success');
        } else {
            showToast(data.error || 'Error al guardar mecánico', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error al guardar mecánico', 'error');
    }
});
