const modal = document.getElementById('modal-detalle-venta');
function abrirModalDetalles() {
    const btnsdetalleVentas = document.querySelectorAll('.detalle-venta');
    btnsdetalleVentas.forEach(btn => {
        btn.addEventListener('click', async () => {
            const codigo = btn.dataset.ventaid ?? btn.dataset.ventam;
            await detalleVentas(codigo);
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100', 'flex');
            }, 5);
        })
    })
}

abrirModalDetalles();

function cerrarModalDetalle() {
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 150);

}

if (document.getElementById('modal-detalle-venta')) {
    document.getElementById('modal-detalle-venta').addEventListener('click', function (e) {
        if (e.target === this) {
            cerrarModal();
        }
    });
}

async function detalleVentas(codigo) {
    try {
        const res = await fetch(`/venta/${decodeURIComponent(codigo)}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            }
        });
        const data = await res.json();
        if (!res.ok) {
            throw data;
        }
        console.log(data)
        setDataDetalleVenta(data);
    } catch (err) {
        console.log(err)
        showToast(`${err.error}`, 'error')
    }
}

function setDataDetalleVenta(data) {
    const fecha = document.getElementById('d-v-fecha');
    const estado = document.getElementById('d-v-estado');
    const mPago = document.getElementById('d-v-pago');
    const codigo = document.getElementById('d-v-codigo');
    const total = document.getElementById('d-v-total');
    const dvCajero = document.getElementById('dv-cajero');
    const dvFactura = document.getElementById('dv-factura');
    dvCajero.innerText = `${data.venta.vendedor.name}`


    const factura = data.venta.factura;
    if (factura) {
        const sucursal = String(factura.sucursal).padStart(3, '0');
        const puntoEmision = String(factura.punto_emision).padStart(3, '0');
        const numero = String(factura.numero).padStart(7, '0');

        dvFactura.innerText = `${sucursal}-${puntoEmision} ${numero}`;

    }
    let metodoDePago = '';
    let estadoClass = data.venta.estado === 'completado' ? 'px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium' :
        (data.venta.estado === 'cancelado' ? 'px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium' :
            'px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium');

    //detalles de la venta
    estado.classList = '';
    const datafecha = new Date(data.venta.created_at);
    fechaFormat = datafecha.toLocaleString('es-PY', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    }).replace(',', ' -');

    codigo.innerText = `Detalle de Venta #${data.venta.codigo}`
    fecha.innerText = fechaFormat
    estado.classList = estadoClass;
    estado.innerText = data.venta.estado

    if (data.productos != '') {
        if (data.venta.pagos.length === 2) {
            metodoDePago = 'mixto'
            document.getElementById('svg-mixto').classList.remove('hidden');
            setMetodosPagosMixto(data);

        } else {
            if (!document.getElementById('svg-mixto').classList.contains('hidden')) {
                document.getElementById('svg-mixto').classList.add('hidden');
            }
            metodoDePago = data.venta.pagos[0].metodo;
        }
        mPago.innerText = metodoDePago;

        if (data.venta.con_descuento) {
            document.getElementById('dv-descuento').classList.remove('hidden');
            document.getElementById('dv-subtotal').innerText = 'Gs. -' + data.venta.monto_descuento.toLocaleString('es-PY');

        }
        if (!data.venta.con_descuento) {
            if (!document.getElementById('dv-descuento').classList.contains('hidden')) {
                document.getElementById('dv-descuento').classList.add('hidden');
            }
        }
    }

    //datos del cliente
    setCliente(data);
    //productos
    setProductos(data);
    //total gral
    setVehiculo(data);
    if (data.productos == '') {
        total.innerText = 'Gs ' + data.venta.monto.toLocaleString('es-PY');
    } else {
        total.innerText = 'Gs ' + data.venta.total.toLocaleString('es-PY');
    }
}

if (document.getElementById('svg-mixto')) {
    document.getElementById('svg-mixto').addEventListener('click', (e) => {
        document.getElementById('d-v-if-mixto').classList.toggle('hidden');
    });
}

let clicks = 0;
if (window.location.pathname == '/movimientos') {
    document.addEventListener('click', () => {
        if (!document.getElementById('d-v-if-mixto').classList.contains('hidden')) {
            clicks++;
            if (clicks >= 2) {
                document.getElementById('d-v-if-mixto').classList.add('hidden');
                clicks = 0;
            }
        } else {
            return;
        }
    });
}

function setMetodosPagosMixto(data) {
    const transf = document.getElementById('d-v-mixto-transf');
    const efectivo = document.getElementById('d-v-mixto-efectivo');
    const total = document.getElementById('d-v-total-mixto');

    if (!transf && !efectivo && !total) {
        return;
    }

    transf.innerText = `Gs. ${data.venta.pagos[1].monto.toLocaleString('es-PY')}`;
    efectivo.innerText = `Gs. ${data.venta.pagos[0].monto.toLocaleString('es-PY')}`;
    total.innerText = `Gs. ${data.venta.total.toLocaleString('es-PY')}`
}

function setCliente(data) {
    if (data.productos != '') {
        const razon = document.getElementById('d-v-razon');
        const rucCi = document.getElementById('d-v-ruc');
        const tel = document.getElementById('d-v-tel');

        console.log(data.venta.cliente);
        razon.innerText = data.venta.cliente.razon_social;
        rucCi.innerText = data.venta.cliente.ruc_ci;
        tel.innerText = data.venta.cliente.telefono ? data.venta.cliente.telefono : 'N/A';
    } else {
        return;
    }
}


function setVehiculo(data) {
    if (!data.venta.vehiculo) {
        document.getElementById('dv-vehiculo-seccion').classList.add('hidden');
        return;
    }
    document.getElementById('dv-vehiculo-seccion').classList.remove('hidden');
    const modelo = document.getElementById('dv-v-modelo');
    const chapa = document.getElementById('dv-v-chapa');
    const color = document.getElementById('dv-v-color');

    console.log(data.venta.vehiculo);
    modelo.innerText = data.venta.vehiculo.marca + ' ' + data.venta.vehiculo.modelo + ' ' + data.venta.vehiculo.anio;
    chapa.innerText = data.venta.vehiculo.patente;
    color.innerText = data.venta.vehiculo.color;
}

function setProductos(data) {
    if (!document.getElementById('d-v-bodyTable')) {
        return;
    }
    const bodyTabla = document.getElementById('d-v-bodyTable');
    bodyTabla.innerHTML = '';
    if (data.productos != '') {
        data.productos.forEach(producto => {
            const tipoClass = producto.tipo === 'servicio'
                ? 'bg-green-100 text-green-800 border border-green-300 '
                : 'bg-blue-100 text-blue-800 border border-blue-300 ';

            const tipoVenta = producto.tipo == 'servicio' ?
                `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                            </svg>` : `<svg role="img" aria-label="Producto - caja" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a1 1 0 0 0-.553-.894l-8-4.5a1 1 0 0 0-.894 0l-8 4.5A1 1 0 0 0 3 8v8a1 1 0 0 0 .553.894l8 4.5a1 1 0 0 0 .894 0l8-4.5A1 1 0 0 0 21 16z"/>
                    <path d="M3.27 6.96 12 11.4l8.73-4.44"/>
                    <path d="M12 11.4v9.6"/>
                </svg>
                `

            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 flex items-center gap-1">
                <span class="rounded-full px-1 py-1 text-xs ${tipoClass}" > ${tipoVenta} </span>
                ${producto.nombre}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                ${producto.codigo ?? 'sin codigo'}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                ${producto.detalles[0].cantidad}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                ${producto.detalles[0].producto_con_descuento ? producto.detalles[0].precio_descuento.toLocaleString('es-PY') : producto.precio_venta.toLocaleString('es-PY')}
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                Gs. ${producto.detalles[0].producto_con_descuento ? (producto.detalles[0].precio_descuento * producto.detalles[0].cantidad).toLocaleString('es-PY') : (producto.precio_venta * producto.detalles[0].cantidad).toLocaleString('es-PY')}
            </td>
            `
            bodyTabla.appendChild(tr);
        })
    } else {

    }
}

if (document.getElementById('dv-buscar')) {
    document.getElementById('dv-buscar').addEventListener('click', () => {
        const desde = document.getElementById('dv-desde').value;
        const hasta = document.getElementById('dv-hasta').value;
        const estado = document.getElementById('dv-i-estado').value;
        const formaPago = document.getElementById('dv-forma-pago').value;
        const tipo = document.getElementById('dv-tipo').value;
        const cliente = document.getElementById('dv-cliente').value;
        const mecanico = document.getElementById('dv-mecanico').value;

        console.log(cliente);

        if (desde == '' && hasta == '' && estado == '' && formaPago == '' && tipo == '' && cliente == '' && mecanico == '') {
            window.location.href = '/movimientos'
            return;
        }

        let datos = JSON.parse(sessionStorage.getItem('datos')) ?? {};
        document.getElementById('dv-borrar-filtros').classList.remove('hidden')
        datos = {
            desde: desde,
            hasta: hasta,
            estado: estado,
            formaPago: formaPago,
            tipo: tipo,
            cliente: cliente,
            mecanico: mecanico,
        }
        sessionStorage.setItem('datos', JSON.stringify(datos))
        buscar();
    });
}

window.addEventListener('DOMContentLoaded', () => {
    let datos = JSON.parse(sessionStorage.getItem('datos'));
    if (datos != null) {
        document.getElementById('dv-borrar-filtros').classList.remove('hidden')
    }
    if (sessionStorage.getItem('datos')) {
        sessionStorage.removeItem('datos')
        if (!document.getElementById('dv-borrar-filtros').classList.contains('hidden')) {
            document.getElementById('dv-borrar-filtros').classList.add('hidden');
        }
    }
})

if (document.getElementById('dv-borrar-filtros')) {
    document.getElementById('dv-borrar-filtros').addEventListener('click', (e) => {
        e.target.classList.add('hidden');
        sessionStorage.removeItem('datos')
        window.location.href = '/movimientos'
    });
}

async function buscar(orderBy = '', direction = '') {
    const datos = JSON.parse(sessionStorage.getItem('datos')) || {};
    const ingresoFiltro = document.getElementById('ingresos-filtro');
    const egresosFiltro = document.getElementById('egresos-filtro');
    const desde = datos.desde ?? '';
    const hasta = datos.hasta ?? '';
    const estado = datos.estado ?? '';
    const formaPago = datos.formaPago ?? '';
    const tipo = datos.tipo ?? '';
    const cliente = datos.cliente ?? '';
    const mecanico = document.getElementById('dv-mecanico').value;
    const q = document.getElementById('dv-input-s').value;
    let paginacion = false;

    if (q === '' && desde == "" && hasta == "" && estado == "" && formaPago == "" && tipo == "" && cliente == "" && mecanico == "") {
        if (!ingresoFiltro.classList.contains('hidden') || !egresosFiltro.classList.contains('hidden')) {
            ingresoFiltro.classList.add('hidden');
            egresosFiltro.classList.add('hidden');
        }
        paginacion = true;
    }
    try {
        const res = await fetch(`/venta?q=${encodeURIComponent(q)}&desde=${encodeURIComponent(desde)}&hasta=${encodeURIComponent(hasta)}&estado=${encodeURIComponent(estado)}&formaPago=${encodeURIComponent(formaPago)}&tipo=${encodeURIComponent(tipo)}&cliente=${encodeURIComponent(cliente)}&mecanico=${encodeURIComponent(mecanico)}&paginacion=${encodeURIComponent(paginacion)}&orderBy=${encodeURIComponent(orderBy)}&direction=${encodeURIComponent(direction)}`);
        const data = await res.json();
        if (!res.ok) {
            throw data;
        }
        console.log(data)
        recargarTablaHistorialVentas(data, paginacion);
    } catch (err) {
        console.log(err)
        showToast(`${err.error}`, 'error')
    }

}
let timerDv;
document.getElementById('dv-input-s').addEventListener('input', e => {
    clearTimeout(timerDv);
    timerDv = setTimeout(async () => {
        buscar();
    }, 500);
})

function recargarTablaHistorialVentas(data, paginacion) {
    // console.log();
    mostrarFiltros(data.filtros);
    const bodyTabla = document.getElementById('dv-body-tabla');
    const ingresoFiltro = document.getElementById('ingresos-filtro');
    const egresosFiltro = document.getElementById('egresos-filtro');
    const montoIngreso = document.getElementById('monto-ingresos-filtro');
    const montoEgreso = document.getElementById('monto-egresos-filtro');
    const svg = `

                                    <span class="cursor-help" title="Venta con descuento">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m8.99 14.993 6-6m6 3.001c0 1.268-.63 2.39-1.593 3.069a3.746 3.746 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043 3.745 3.745 0 0 1-3.068 1.593c-1.268 0-2.39-.63-3.068-1.593a3.745 3.745 0 0 1-3.296-1.043 3.746 3.746 0 0 1-1.043-3.297 3.746 3.746 0 0 1-1.593-3.068c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 0 1 1.043-3.297 3.745 3.745 0 0 1 3.296-1.042 3.745 3.745 0 0 1 3.068-1.594c1.268 0 2.39.63 3.068 1.593a3.745 3.745 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.297 3.746 3.746 0 0 1 1.593 3.068ZM9.74 9.743h.008v.007H9.74v-.007Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                    </span>
    `;




    bodyTabla.innerText = ''
    if (data.ingresos_filtro != null || data.ingresos_filtro != undefined) {
        ingresoFiltro.classList.remove('hidden');
        montoIngreso.innerText = `Ingresos: Gs. ${data.ingresos_filtro.toLocaleString('es-PY')}`
    }
    if ((data.egresos_filtro != null || data.egresos_filtro != undefined) && data.egresos_filtro != 0) {
        egresosFiltro.classList.remove('hidden');
        montoEgreso.innerText = `Egresos: Gs. ${data.egresos_filtro.toLocaleString('es-PY')}`
    }
    data.ventas.forEach(venta => {
        if (venta.venta_id != null) {
            let estadoClass = '';
            const tr = document.createElement('tr');
            tr.classList = 'hover:bg-gray-50 transition-colors';
            venta.venta.estado === 'completado' ? estadoClass = 'bg-green-100 text-green-800' :
                (venta.venta.estado == 'cancelado' ? estadoClass = 'bg-red-100 text-red-800' : estadoClass = 'bg-yellow-100 text-yellow-800');

            const anularVenta = venta.venta.estado != 'cancelado' ? `
                                            <button data-id="${venta.venta.codigo}"
                                                class="cancelar-venta text-red-600 hover:text-red-900 cursor-pointer" title="Cancelar">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                                    <path fill-rule="evenodd"
                                                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
            ` : '';


            let fechaC = new Date(venta.venta.created_at)
            let productos;
            let movimientos;
            if (venta.venta.productos) {
                productos = venta.venta.productos
                    .map(p => `<p class="font-semibold text-start block">● ${p.nombre}</p>`)
                    .join('');
            }
            if (productos == undefined) {
                if (venta.concepto != 'Venta de productos') {
                    movimientos = venta.concepto
                }
            }

            fecha = fechaC.toLocaleString('es-PY', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }).replace(',', ' -');

            tr.innerHTML = `
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="venta-id px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 items-center">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="bg-blue-200 px-2 py-0.5 text-xs font-semibold text-blue-800 rounded-xl">
                                                Venta
                                            </span>
                                            ${venta.venta.con_descuento ? svg : ''}                                            
                                        </div>
                                        <span class="" title="Codigo de venta">
                                            #${venta.venta.codigo}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ${fecha}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-semibold">
                                        ${venta.venta.cliente.razon_social ?? venta.venta.cliente.name}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ${movimientos ?? productos}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Gs. ${venta.venta.total.toLocaleString('es-PY')}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${estadoClass}">
                                            ${venta.venta.estado}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button id="btn-detalle-venta" data-ventaId="${venta.venta.codigo}"
                                                class="detalle-venta cursor-pointer text-blue-600 hover:text-blue-900" title="Ver detalles">
                                                <i class="fas fa-eye">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                                        <path fill-rule="evenodd"
                                                            d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </i>
                                            </button>
                                            <button class="text-green-600 cursor-pointer hover:text-green-900" title="Imprimir">
                                                <i class="fas fa-print">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                                        <path fill-rule="evenodd"
                                                            d="M7.875 1.5C6.839 1.5 6 2.34 6 3.375v2.99c-.426.053-.851.11-1.274.174-1.454.218-2.476 1.483-2.476 2.917v6.294a3 3 0 0 0 3 3h.27l-.155 1.705A1.875 1.875 0 0 0 7.232 22.5h9.536a1.875 1.875 0 0 0 1.867-2.045l-.155-1.705h.27a3 3 0 0 0 3-3V9.456c0-1.434-1.022-2.7-2.476-2.917A48.716 48.716 0 0 0 18 6.366V3.375c0-1.036-.84-1.875-1.875-1.875h-8.25ZM16.5 6.205v-2.83A.375.375 0 0 0 16.125 3h-8.25a.375.375 0 0 0-.375.375v2.83a49.353 49.353 0 0 1 9 0Zm-.217 8.265c.178.018.317.16.333.337l.526 5.784a.375.375 0 0 1-.374.409H7.232a.375.375 0 0 1-.374-.409l.526-5.784a.373.373 0 0 1 .333-.337 41.741 41.741 0 0 1 8.566 0Zm.967-3.97a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H18a.75.75 0 0 1-.75-.75V10.5ZM15 9.75a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75V10.5a.75.75 0 0 0-.75-.75H15Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </i>
                                            </button>
                                            ${anularVenta}
                                        </div>
                                    </td>
                                </tr>

                                `;
            bodyTabla.appendChild(tr);
        } else {
            const tr = document.createElement('tr');
            tr.classList = 'hover:bg-gray-50 transition-colors';
            let fechaC = new Date(venta.created_at)

            const tipoClass = venta.tipo === 'egreso' ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800';
            const montoClass = venta.tipo === 'egreso' ? 'text-red-500' : 'text-gray-900';
            const menos = venta.tipo === 'egreso' ? '-' : '';

            fecha = fechaC.toLocaleString('es-PY', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }).replace(',', ' -');

            tr.innerHTML = `
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="venta-id px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 items-center">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-xl ${tipoClass} ">
                                                ${venta.tipo}
                                            </span>
                                            
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ${fecha}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="bg-gray-200 text-gray-500 font-semibold px-2 py-0.5 rounded-xl italic" > Sin Cliente </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ${venta.concepto}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium ${montoClass}">        
                                        ${menos}  Gs. ${venta.monto.toLocaleString('es-PY')}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button id="btn-detalle-movimiento" data-ventaM="${venta.id}"
                                                class="detalle-movimiento cursor-pointer text-blue-600 hover:text-blue-900" title="Ver detalles">
                                                <i class="fas fa-eye">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                                        <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                                        <path fill-rule="evenodd"
                                                            d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </i>
                                            </button>
                                            <button class="text-green-600 cursor-pointer hover:text-green-900" title="Imprimir">
                                                <i class="fas fa-print">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                                        <path fill-rule="evenodd"
                                                            d="M7.875 1.5C6.839 1.5 6 2.34 6 3.375v2.99c-.426.053-.851.11-1.274.174-1.454.218-2.476 1.483-2.476 2.917v6.294a3 3 0 0 0 3 3h.27l-.155 1.705A1.875 1.875 0 0 0 7.232 22.5h9.536a1.875 1.875 0 0 0 1.867-2.045l-.155-1.705h.27a3 3 0 0 0 3-3V9.456c0-1.434-1.022-2.7-2.476-2.917A48.716 48.716 0 0 0 18 6.366V3.375c0-1.036-.84-1.875-1.875-1.875h-8.25ZM16.5 6.205v-2.83A.375.375 0 0 0 16.125 3h-8.25a.375.375 0 0 0-.375.375v2.83a49.353 49.353 0 0 1 9 0Zm-.217 8.265c.178.018.317.16.333.337l.526 5.784a.375.375 0 0 1-.374.409H7.232a.375.375 0 0 1-.374-.409l.526-5.784a.373.373 0 0 1 .333-.337 41.741 41.741 0 0 1 8.566 0Zm.967-3.97a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H18a.75.75 0 0 1-.75-.75V10.5ZM15 9.75a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75V10.5a.75.75 0 0 0-.75-.75H15Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900 cursor-pointer" title="Eliminar">
                                                <i class="fas fa-trash">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                                        <path fill-rule="evenodd"
                                                            d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
        `;
            bodyTabla.appendChild(tr);
        }
    });
    const paginate = document.getElementById('paginacion')
    if (paginacion == false) {
        paginate.classList.add('hidden');
    } else {
        if (paginate.classList.contains('hidden')) {
            paginate.classList.remove('hidden');
        }
    }
    abrirmodalDmDetalles();
    abrirModalDetalles();
    anularVenta();
    eliminarMov();
}


function mostrarFiltros(filtros) {
    const dvFiltros = document.getElementById('dv-filtros');
    const dvFiltrosTexto = document.getElementById('dv-filtros-texto');
    const dvFiltrosBusqueda = document.getElementById('dv-filtros-busqueda');
    const dvFiltrosFecha = document.getElementById('dv-filtros-fecha');
    const dvFiltrosMetodo = document.getElementById('dv-filtros-metodo');
    const dvFiltrosEstado = document.getElementById('dv-filtros-estado');
    const dvFiltrosTipo = document.getElementById('dv-filtros-tipo');
    const dvFiltrosCliente = document.getElementById('dv-filtros-cliente');
    const dvFiltrosMecanico = document.getElementById('dv-filtros-mecanico');
    const dvFiltrosResultados = document.getElementById('dv-filtros-resultados');
    const dvFiltrosCantidad = document.getElementById('dv-filtros-cantidad');

    dvFiltros.classList.remove('hidden');
    if (!filtros) {
        dvFiltrosTexto.classList.add('hidden');
        dvFiltrosBusqueda.classList.add('hidden');
        dvFiltrosFecha.classList.add('hidden');
        dvFiltrosMetodo.classList.add('hidden');
        dvFiltrosEstado.classList.add('hidden');
        dvFiltrosTipo.classList.add('hidden');
        dvFiltrosCliente.classList.add('hidden');
        dvFiltrosMecanico.classList.add('hidden');
        dvFiltrosResultados.classList.add('hidden');
        dvFiltrosCantidad.classList.add('hidden');
        return;
    }
    if (filtros.query != null) {
        dvFiltrosTexto.classList.remove('hidden');
        dvFiltrosBusqueda.classList.remove('hidden');
        dvFiltrosBusqueda.textContent = `Busqueda: ${filtros.query}`;
    }
    if (filtros.desde != null || filtros.hasta != null) {
        dvFiltrosFecha.classList.remove('hidden');
        dvFiltrosFecha.textContent = `Fecha: ${filtros.desde || 'Hoy'} - ${filtros.hasta || ''}`;
    }
    if (filtros.formaPago != null) {
        dvFiltrosMetodo.classList.remove('hidden');
        dvFiltrosMetodo.textContent = `Forma de pago: ${filtros.formaPago}`;
    }
    if (filtros.estado != null) {
        dvFiltrosEstado.classList.remove('hidden');
        dvFiltrosEstado.textContent = `Estado: ${filtros.estado}`;
    }
    if (filtros.tipo != null) {
        dvFiltrosTipo.classList.remove('hidden');
        dvFiltrosTipo.textContent = `Tipo de venta: ${filtros.tipo}`;
    }
    if (filtros.cliente != null) {
        dvFiltrosCliente.classList.remove('hidden');
        dvFiltrosCliente.textContent = `Cliente: ${filtros.cliente}`;
    }
    if (filtros.mecanico != null) {
        dvFiltrosMecanico.classList.remove('hidden');
    }
    dvFiltrosResultados.classList.remove('hidden');
    dvFiltrosCantidad.textContent = `${filtros.resultados}`;
}

const trigger = document.getElementById('dropdown');
const exportMenu = document.getElementById('export-menu');
const iconFlecha = document.getElementById('icon-flecha');

trigger.addEventListener('click', (e) => {
    //e.stopPropagation(); 
    iconFlecha.classList.toggle('rotate-180');

    if (exportMenu.classList.contains('hidden')) {
        exportMenu.classList.remove('hidden');
        setTimeout(() => {
            exportMenu.classList.remove('opacity-0');
            exportMenu.classList.add('opacity-100');
        }, 10);
    } else {
        exportMenu.classList.add('hidden');
        setTimeout(() => {
            exportMenu.classList.remove('opacity-100');
            exportMenu.classList.add('opacity-0');
        }, 10);
    }
});

document.addEventListener('click', (e) => {
    if (!exportMenu.classList.contains('hidden') && !trigger.contains(e.target) && !exportMenu.contains(e.target)) {
        exportMenu.classList.add('hidden');
        exportMenu.classList.remove('opacity-100');
        exportMenu.classList.add('opacity-0');

        iconFlecha.classList.remove('rotate-180');
    }
});


// function toastLoading(message = "Generando PDF") {
//     const container = document.getElementById('loading-container');
//     if (container.classList.contains('hidden')) {
//         container.classList.remove('hidden');
//     }
//     sessionStorage.setItem('pdf-toast', JSON.stringify(true))
//     const spinner = `
//         <svg class="animate-spin w-8 h-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
//             <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
//             <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
//         </svg>`;

//     const toast = document.createElement('div');
//     toast.className = `bg-blue-500 font-semibold text-white px-5 py-3 rounded-lg shadow-lg flex items-center space-x-2 opacity-0 transition-all duration-300`;
//     toast.innerHTML = `<span class="spinner">${spinner}</span><span class="toast-message">${message}</span>`;

//     container.appendChild(toast);

//     setTimeout(() => toast.classList.remove('opacity-0'), 10);

//     window.Echo.private(`pdf-ready.${window.userId}`)
//         .listen('PdfGeneradoEvent', async (e) => {
//             const messageEl = toast.querySelector('.toast-message');
//             const spinnerEl = toast.querySelector('.spinner');

//             spinnerEl.innerHTML = `
//                 <svg class="w-8 h-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
//                     <path stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
//                 </svg>`;

//             messageEl.textContent = "PDF generado correctamente";
//             toast.classList.remove('bg-blue-500');
//             toast.classList.add('bg-green-500');

//             setTimeout(() => {
//                 toast.classList.add('opacity-0');
//                 sessionStorage.removeItem('pdf-toast')
//                 setTimeout(() => toast.remove(), 500);
//             }, 1500);
//         });
// }

document.getElementById('export-pdf').addEventListener('click', async () => {
    toastLoading('Generando PDF, te avisaremos cuando esté listo.');
    try {
        const res = await fetch('/export-pdf');
        const data = await res.json();
        if (!res.ok) {
            throw data;
        }
        console.log(data)
    } catch (err) {
        console.log(err)
    }
});

if (document.querySelectorAll('.cancelar-venta')) {
    anularVenta();
}
function anularVenta() {
    const btnsCancelar = document.querySelectorAll('.cancelar-venta');
    btnsCancelar.forEach(btn => {
        btn.addEventListener('click', async () => {
            const modal = document.getElementById('modal-confirmar-anulacion-venta');
            const title = document.getElementById('h3-anular-venta')
            const btnAnular = document.getElementById('btn-anular-venta');
            modal.classList.remove('hidden');
            try {
                const id = btn.dataset.id;
                const res = await fetch('/venta/' + id)
                const data = await res.json();
                if (!res.ok) {
                    throw data;
                }

                title.innerText = `Estas seguro de anular la venta con código: #${data.venta.codigo}`
                btnAnular.dataset.id = data.venta.id;

                const dataF = new FormData();
                dataF.append('estado', 'cancelado');

                btnAnular.addEventListener('click', async () => {
                    const res = await fetch(`/api/venta-update/${btnAnular.dataset.id}`, {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: dataF
                    });
                    const datad = await res.json();
                    if (!res.ok) {
                        throw datad
                    }
                    console.log(datad)
                    window.location.reload();
                }, { once: true });
            } catch (err) {
                console.error(err)
            }
        });
    })


    const cerrarModalAnVe = document.querySelectorAll('.cerrar-modal-an-ven')
    cerrarModalAnVe.forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('modal-confirmar-anulacion-venta').classList.add('hidden');
        })
    })
}

if (document.querySelectorAll('.eliminar-mov')) {
    eliminarMov();
}

function eliminarMov() {
    const btnEliminarMov = document.querySelectorAll('.eliminar-mov');

    btnEliminarMov.forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = document.getElementById('modal-confirmar-borrar-mov');
            const id = btn.dataset.id
            const title = document.getElementById('h3-borrar-mov');
            const btnConfirmar = document.getElementById('btn-borrar-mov');
            btnConfirmar.dataset.id = id;
            const tipo = document.querySelector(`span[id="${id}"]`).textContent.trim();
            modal.classList.remove('hidden');
            const span = document.createElement('span');
            span.className = 'block text-xs text-red-700';
            span.innerText = `Esta acción es irreversible`;
            title.innerText = `Estas Seguro de eliminar este ${tipo}`
            title.appendChild(span);

            btnConfirmar.addEventListener('click', async () => {
                try {

                    const res = await fetch(`/api/eliminar-mov/${id}`, {
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        }
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        throw data;
                    }

                    window.location.reload();
                } catch (err) {
                    console.error(err)
                }
            });
        });
    });


    const cerrarModalEliMov = document.querySelectorAll('.cerrar-borrar-mov');
    cerrarModalEliMov.forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('modal-confirmar-borrar-mov').classList.add('hidden');
        })
    });
}