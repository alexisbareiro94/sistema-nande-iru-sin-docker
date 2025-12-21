const $ = el => document.querySelector(el);
const $$ = el => document.querySelectorAll(el);
const $i = el => document.getElementById(el);

const $el = (el, event, callback) => {
    return document.querySelector(el).addEventListener(event, callback)
}

const $eli = (el, event, callback) => {
    return document.getElementById(el).addEventListener(event, callback)
}

const btnAbrirCaja = document.getElementById('btn-abrir-caja');
const modalAbrirCaja = document.getElementById('modalAbrirCaja')
const cerrarModalCaja = document.querySelectorAll('#cancelarModal, #closeModal')
const abrirCajaForm = document.getElementById('abrir-caja-form')

const modalVentas = document.getElementById('modal-ventas');
const btnCerrarModalVentas = document.getElementById('cerrar-modal-ventas');

if (btnAbrirCaja) {
    btnAbrirCaja.addEventListener('click', () => {
        modalAbrirCaja.classList.remove('hidden')
        document.getElementById('monto_inicial').focus();
    });
}

if (cerrarModalCaja) {
    cerrarModalCaja.forEach(btn => {
        btn.addEventListener('click', () => {
            modalAbrirCaja.classList.add('hidden');
        })
    });
}

if (document.getElementById('ir-a-ventas')) {
    document.getElementById('ir-a-ventas').addEventListener('click', () => {
        document.body.classList.add('overflow-hidden');
        modalVentas.classList.remove('hidden');
        const carrito = JSON.parse(sessionStorage.getItem('carrito'));
        if (carrito == null) {
            document.getElementById('input-b-producto-ventas').focus();
        } else if (carrito != null) {
            document.getElementById('i-ruc-ci').focus();
        }
    });
}

if (btnCerrarModalVentas) {
    btnCerrarModalVentas.addEventListener('click', () => {
        document.body.classList.remove('overflow-hidden');
        modalVentas.classList.add('hidden');
    });
}

if (document.getElementById('form-b-productos-ventas')) {
    document.getElementById('form-b-productos-ventas').addEventListener('submit', (e) => {
        e.preventDefault();
    })
}

let timerVentas;
if (document.getElementById('input-b-producto-ventas')) {
    document.getElementById('input-b-producto-ventas').addEventListener('input', (e) => {
        clearTimeout(timerVentas);
        timerVentas = setTimeout(async () => {
            let query = e.target.value.trim();
            if (query.length == 0) {
                const tablaVentaProductos = document.getElementById('tabla-venta-productos');
                tablaVentaProductos.innerHTML = '';
            } else {
                try {
                    const res = await fetch(`/api/productos?q=${encodeURIComponent(query)}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                        }
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        throw data;
                    }
                    if (data.productos && Object.keys(data.productos).length > 0) {
                        await recargarTablaVentas(data);
                    } else {
                        const tablaVentaProductos = document.getElementById('tabla-venta-productos');
                        tablaVentaProductos.innerHTML = `
                        <div class="col-span-full flex flex-col items-center justify-center py-12 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <p class="text-sm font-medium">No hay resultados</p>
                        </div>
                        `;
                    }

                } catch (err) {
                    showToast(`${err.messages}`, 'error');
                }
            }
        }, 300);
    });
}

async function recargarTablaVentas(data) {
    const tablaVentaProductos = document.getElementById('tabla-venta-productos');
    tablaVentaProductos.innerHTML = '';

    data.productos.forEach(producto => {
        const card = document.createElement('div');
        const stockClass = producto.tipo == 'servicio' ? 'text-gray-400' : producto.stock < producto.stock_minimo ? 'text-red-500' : 'text-green-600';
        const stockBgClass = producto.tipo == 'servicio' ? 'bg-gray-100' : producto.stock < producto.stock_minimo ? 'bg-red-50' : 'bg-green-50';

        card.className = 'productos select-product-mobil bg-white border border-gray-200 rounded-xl p-3 hover:shadow-lg hover:border-gray-300 transition-all cursor-pointer active:scale-95 flex flex-col justify-between';
        card.dataset.producto = JSON.stringify(producto);
        const tipoBadgeClass = producto.tipo == 'servicio' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700';
        const tipoBadgeText = producto.tipo == 'servicio' ? 'Servicio' : 'Producto';

        card.innerHTML = `
            <div class="flex flex-col h-full relative">
                <span class="absolute -top-1 -right-1 text-[10px] font-bold px-2 py-0.5 rounded-full ${tipoBadgeClass}">
                    ${tipoBadgeText}
                </span>
                <div class="flex items-center justify-center mb-2 mt-2">
                    <div class="bg-gradient-to-br from-gray-100 to-gray-200 p-3 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1 text-center mb-2">
                    <p class="font-semibold text-gray-800 text-sm leading-tight line-clamp-2">${producto.nombre}</p>
                    <p class="text-xs text-gray-400 mt-1">${producto.codigo ?? 'Sin código'}</p>
                </div>
                <div class="space-y-2">
                    <div class="text-center">
                        <span class="text-lg font-bold text-gray-800">Gs. ${producto.precio_venta.toLocaleString('es-PY')}</span>
                    </div>
                    <div class="flex items-center justify-center">
                        <span class="text-xs px-2 py-1 rounded-full ${stockBgClass} ${stockClass} font-medium">
                            ${producto.tipo == 'servicio' ? '🔧 Servicio' : '📦 Stock: ' + producto.stock}
                        </span>
                    </div>
                </div>
                <button data-producto='${JSON.stringify(producto)}' class="productos mt-3 w-full bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold py-2 px-3 rounded-lg flex items-center justify-center gap-1 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar
                </button>
            </div>
        `;
        tablaVentaProductos.appendChild(card);
    });
}

function addToCart() {
    const tablaVentaProductos = document.getElementById('tabla-venta-productos');
    tablaVentaProductos.addEventListener('click', function (e) {
        const btn = e.target.closest('.productos');
        if (btn) {
            const producto = JSON.parse(btn.dataset.producto);
            let carrito = JSON.parse(sessionStorage.getItem('carrito')) || {};

            if (carrito[producto.id]) {
                carrito[producto.id].cantidad += 1;

            } else {
                carrito[producto.id] = {
                    nombre: producto.nombre,
                    codigo: producto.codigo,
                    precio: producto.precio_venta,
                    stock: producto.stock,
                    imagen: producto.imagen,
                    descuento: false,
                    precio_descuento: 0,
                    cantidad: 1
                };
            }
            showToast('Producto Agregado');
            sessionStorage.setItem('carrito', JSON.stringify(carrito));
            renderCarrito();
        }
    });
}
addToCart();
renderCarrito();

function renderCarrito() {
    const carrito = JSON.parse(sessionStorage.getItem('carrito')) || {};
    const carritoForm = document.getElementById('carrito-form');
    carritoForm.innerHTML = '';

    Object.entries(carrito).forEach(([id, producto]) => {
        const div = document.createElement('div');
        div.classList.add('flex-1')
        div.innerHTML = `
            <div id="carrito-container" class="bg-gray-50 p-2 flex justify-between items-start border-b border-gray-300">
                <div class="flex-1">
                    <p class="text-xs font-semibold">${producto.nombre}</p>
                    <p class="text-xs text-gray-500">Código: ${producto.codigo}</p>
                </div>
                <div id="btn-cont" class="flex items-center gap-0 ml-1">
                    <button data-action="dec" data-id="${id}" class="decaum w-5 h-5 rounded-md cursor-pointer bg-gray-200 text-gray-700 flex items-center justify-center hover:bg-gray-200 transition-colors">
                        <span>-</span>
                    </button>
                    <span class="w-5 text-center font-medium">${producto.cantidad}</span>
                    <button data-action="aum" data-id="${id}" class="decaum w-5 h-5 rounded-md cursor-pointer bg-gray-400 text-white flex items-center justify-center hover:bg-gray-600 transition-colors">
                        <span>+</span>
                    </button>
                </div>
                <div class="ml-3 font-medium flex-col gap-1">
                    Gs. <input data-id="${id}" class="input-precio max-w-25 border border-white hover:border-gray-300 focus:border-gray-200 px-2" type="text"
                        value="${producto.descuento ? (producto.precio_descuento * producto.cantidad).toLocaleString('es-PY') : (producto.precio * producto.cantidad).toLocaleString('es-PY')}">
                        ${producto.descuento ?
                `<div class="text-xs ml-2 -mt-1.5 text-red-600 font-normal">Gs. ${producto.precio.toLocaleString('es-PY')}</div>`
                : ''
            }
                </div>
            </div>
        `;
        carritoForm.appendChild(div);
        descuento();
        totalCarrito();
    });
}

function descuento() {
    let timerInPrecio;
    document.querySelectorAll('.input-precio').forEach(input => {
        input.addEventListener('input', () => {
            const nuevoPrecio = input.value;
            const id = input.dataset.id;

            clearTimeout(timerInPrecio);
            timerInPrecio = setTimeout(() => {
                carrito = JSON.parse(sessionStorage.getItem('carrito')) || {};
                carrito[id].descuento = true;
                carrito[id].precio_descuento = nuevoPrecio;

                if (nuevoPrecio == carrito[id].precio) {
                    carrito[id].descuento = false;
                    carrito[id].precio_descuento = 0;
                }
                sessionStorage.setItem('carrito', JSON.stringify(carrito))
                totalCarrito();
                renderCarrito();
            }, 1000)

        })
    })
}

if (document.getElementById('carrito-form')) {
    document.getElementById('carrito-form').addEventListener('click', (e) => {
        e.preventDefault();
        if (e.target.closest('.decaum')) {
            const carrito = JSON.parse(sessionStorage.getItem('carrito')) || {};
            const btn = e.target.closest('.decaum');
            const action = btn.dataset.action;
            const id = btn.dataset.id;

            if (action === 'aum') {
                carrito[id].cantidad++;
            } else {
                carrito[id].cantidad--;
                if (carrito[id].cantidad <= 0) {
                    delete carrito[id];
                    let totalVenta = document.getElementById('totalCarrito');
                    totalVenta.innerHTML = '';
                    document.getElementById('subTotalCarrito').innerHTML = '';
                }
            }

            sessionStorage.setItem('carrito', JSON.stringify(carrito));
            renderCarrito();
        }
    });
}

function totalCarrito() {
    let totalP = 0;
    let subTotal = 0;
    let cantidadTotal = 0;
    let totalVenta = document.getElementById('totalCarrito');
    const subtotalVenta = document.getElementById('subTotalCarrito');
    const carrito = JSON.parse(sessionStorage.getItem('carrito')) || {};

    Object.entries(carrito).forEach(([id, producto]) => {
        if (carrito[id].descuento && carrito[id].precio_descuento > 0) {
            totalP += producto.cantidad * producto.precio_descuento;
        } else {
            totalP += producto.cantidad * producto.precio;
        }
        cantidadTotal += producto.cantidad;
        subTotal += producto.cantidad * producto.precio;
    })

    totalCarritoSession = JSON.parse(sessionStorage.getItem('totalCarrito')) || {};
    totalCarritoSession = {
        total: totalP,
        subtotal: subTotal,
        cantidadTotal: cantidadTotal,
    };
    sessionStorage.setItem('totalCarrito', JSON.stringify(totalCarritoSession));

    subtotalVenta.innerHTML = `Gs. ${subTotal.toLocaleString('es-PY')}`;
    totalVenta.innerHTML = `Gs. ${totalP.toLocaleString('es-PY')}`;
}

const form = document.getElementById('form-cliente-venta');
const modalUsuarios = document.getElementById('modalUsuarios');

if (form) {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const inputRucCi = document.getElementById('i-ruc-ci').value;
        const inputNombreRazon = document.getElementById('i-nombre-razon').value;
        const listaUsers = document.getElementById('listaUsuarios');
        listaUsers.innerHTML = '';
        const q = inputRucCi ?? inputNombreRazon;
        if (q.length <= 0) {
            return;
        }
        try {
            const res = await fetch(`/api/users?q=${encodeURIComponent(q)}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                }
            });

            const data = await res.json();
            if (!res.ok) {
                throw data;
            }
            if (data.users && Object.keys(data.users).length > 0) {
                data.users.forEach(user => {
                    const li = document.createElement('li');
                    li.classList = 'clientes hover:bg-gray-100 px-2 py-2 cursor-pointer bg-gray-200 rounded-md';
                    li.dataset.razon = user.razon_social;
                    li.dataset.ruc = user.ruc_ci;
                    li.innerHTML = `
                        <p> <strong> Nombre:</strong> ${user.razon_social}</p>
                        <p> <strong> RUC/CI:</strong> ${user.ruc_ci}</p>
                `;
                    listaUsers.appendChild(li);
                    selectUser();
                });
            } else {
                listaUsers.innerHTML = `
            <div class="items-center justify-center text-center">
                <p class="text-center text-gray-400">No hay registro</p>
                <br>
            </div>
            `;
            }

        } catch (err) {
            showToast(`${err.error}`, 'error');
        }
        modalUsuarios.classList.remove('hidden')
    });
}

function selectUser() {
    const listaUsers = document.getElementById('listaUsuarios');

    const inputRazonSocial = document.getElementById('i-nombre-razon');
    const inputRucCi = document.getElementById('i-ruc-ci');

    listaUsers.addEventListener('click', (e) => {
        const btn = e.target.closest('.clientes');
        if (btn) {
            const razon = btn.dataset.razon;
            const ruc = btn.dataset.ruc;

            inputRazonSocial.value = razon;
            inputRucCi.value = ruc;

            document.getElementById('modalUsuarios').classList.add('hidden');
        }
    });
}

const formAddCliente = document.getElementById('form-add-cliente');

if (formAddCliente) {
    formAddCliente.addEventListener('submit', async (e) => {
        e.preventDefault();
        const listaUsers = document.getElementById('listaUsuarios');
        const addCliente = new FormData();
        addCliente.append('razon_social', document.getElementById('razon_social').value.trim());
        addCliente.append('ruc_ci', document.getElementById('ruc_ci').value.trim())
        addCliente.append('email', document.getElementById('correo-c').value.trim());
        addCliente.append('telefono', document.getElementById('telefono-c').value.trim());

        try {
            const res = await fetch(`/api/users`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: addCliente,
            });

            const data = await res.json();
            console.log(data);

            if (!res.ok) {
                throw data;
            }
            console.log(data)
            const inputRazonSocial = document.getElementById('i-nombre-razon');
            const inputRucCi = document.getElementById('i-ruc-ci');
            const razon = data.cliente.razon_social;
            const ruc = data.cliente.ruc_ci;

            inputRazonSocial.value = razon;
            inputRucCi.value = ruc;

            document.getElementById('modalUsuarios').classList.add('hidden');
            document.getElementById('modal-add-cliente').classList.add('hidden');

            showToast('Cliente Agregado con éxito', 'success');
        } catch (err) {
            console.log(err)
            showToast(`${err.error}`, 'error');
        }
    })
}

window.addEventListener('DOMContentLoaded', async () => {
    await recargarSaldo();
});

async function recargarSaldo(flag = true) {
    const saldo = document.getElementById('saldo-caja');
    if (!saldo) {
        return;
    }
    try {
        const res = await fetch(`/api/movimiento/total`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
        });
        const data = await res.json();
        if (!res.ok) {
            throw data;
        }
        if (flag === true) {
            saldo.innerText = `${data.total.toLocaleString('es-PY')} GS.`;
        }
        return data;
    } catch (err) {
        showToast(`${err.error}`, 'error')
    }
}

if (document.getElementById('btn-movimiento')) {
    document.getElementById('btn-movimiento').addEventListener('click', () => {
        document.getElementById('modal-movimiento-caja').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

    });
}

if (document.getElementsByName('tipo-movimiento')) {
    document.getElementsByName('tipo-movimiento').forEach(btn => {
        const salarioCont = document.getElementById('salario-cont');
        const conceptomm = document.getElementById('concepto-mm');
        btn.addEventListener('click', () => {
            if (btn.value == 'salario') {
                salarioCont.classList.remove('hidden');
                conceptomm.value = 'Pago de salario'
                conceptomm.disabled = true;
            } else {
                if (!salarioCont.classList.contains('hidden')) {
                    salarioCont.classList.add('hidden');
                }
                conceptomm.value = ''
                conceptomm.disabled = false;
            }
        });
    });
}

const select = document.getElementById('personales');
let personal = '';

if (select) {
    select.addEventListener('change', () => {
        personal = select.value;
    });
}

if (document.getElementById('confirmar-movimiento')) {


    document.getElementById('confirmar-movimiento').addEventListener('click', async (e) => {
        e.preventDefault();
        let errores = '';
        let tipoSelcted;
        const form = document.getElementById('movimiento-form');
        const tipos = document.getElementsByName('tipo-movimiento');
        const concepto = document.getElementById('concepto-mm');
        const monto = document.getElementById('monto-mm');

        tipos.forEach(tipo => {
            if (tipo.checked) {
                tipoSelcted = tipo.value;
            }
        })
        tipoSelcted == 'salario' ? tipoSelcted = 'egreso' : '';


        if (tipoSelcted == undefined) {
            showToast('Debes Seleccionar un tipo de movimiento', 'error');
            errores = '1'
        }
        if (concepto.value == '') {
            showToast('Debes Ingresar un concepto', 'error');
            errores = '2';
        }
        if (monto.value == '') {
            showToast('Debes Ingresar un monto', 'error');
            errores = '3';
        }
        if (errores != '') {
            return;
        }
        try {
            const formData = new FormData();
            formData.append('tipo', tipoSelcted);
            formData.append('concepto', concepto.value);
            formData.append('monto', monto.value);
            formData.append('personal_id', personal);

            const res = await fetch('/api/movimiento', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            });
            const data = await res.json();
            if (!res.ok) {
                throw data
            }
            console.log(data)
            personal = '';
            form.reset();
            document.getElementById('modal-movimiento-caja').classList.add('hidden');
            document.getElementById('datos-personal').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            limpiarUI();
            showToast('Movimiento registrado');
        } catch (err) {
            showToast(`${err.error}`, 'error')
        }
    });
}

//cierre de caja
if (document.getElementById('btn-cerrar-caja')) {
    document.getElementById('btn-cerrar-caja').addEventListener('click', async () => {
        const modalCierreCaja = document.getElementById('modalCierreCaja');
        modalCierreCaja.classList.remove('hidden')
        document.body.classList.add('overflow-hidden');
        await recargarCierreCaja();
    });
}


if (document.getElementById('btn-cerrar-caja')) {
    document.getElementById('modalCierreCaja').addEventListener('click', e => {
        if (e.target == document.getElementById('modalCierreCaja')) {
            document.getElementById('modalCierreCaja').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    });
}

async function recargarCierreCaja() {
    const nombreCC = document.getElementById('nombre-cc');
    const fechaCC = document.getElementById('fecha-cc');
    const montoInicialCC = document.getElementById('monto-inicial-cc')
    const ingresos = document.getElementById('ingresos-cc');
    const egresos = document.getElementById('egresos-cc');
    const saldoEsperado = document.getElementById('saldo-esperado');

    try {
        const res = await fetch('/api/movimiento/total', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            }
        });
        const data = await res.json();
        if (!res.ok) {
            throw data;
        }

        fechaA = new Date(data.caja.created_at);
        fechaFormateadaA = fechaA.toLocaleString('es-PY', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }).replace(',', ' -');

        nombreCC.innerText = `${data.caja.user.name}`
        fechaCC.innerText = `${fechaFormateadaA}`
        montoInicialCC.innerText = `${data.caja.monto_inicial.toLocaleString('es-PY')} Gs.`
        ingresos.innerText = `${data.ingreso.toLocaleString('es-PY')} GS.`
        egresos.innerText = `${data.egreso.toLocaleString('es-PY')} GS.`
        saldoEsperado.innerText = `${(data.ingreso - data.egreso).toLocaleString('es-PY')} Gs.`
    } catch (err) {
        showToast(`${err.error}`, 'error')
    }
}

let timerCC;

if (document.getElementById('montoContado')) {
    document.getElementById('montoContado').addEventListener('input', async (e) => {
        const data = await recargarSaldo(false);
        const montoContado = document.getElementById('montoContado').value
        const diferencia = document.getElementById('diferencia');
        const saldoEsperado = data.ingreso - data.egreso;

        e.preventDefault();
        clearTimeout(timerCC)
        if (montoContado == '') {
            diferencia.innerText = '0 GS.'
            return;
        }
        timerCC = setTimeout(() => {
            diferencia.innerText = (montoContado - saldoEsperado).toLocaleString('es-PY') + ' GS.';
        }, 800)
    })
}

if (document.getElementById('confirmar-cierre')) {
    document.getElementById('confirmar-cierre').addEventListener('click', async () => {
        const data = await recargarSaldo(false);
        const montoContado = document.getElementById('montoContado').value
        const observaciones = document.getElementById('observaciones').value;
        const saldoEsperado = data.ingreso - data.egreso;
        const diferencia = montoContado - saldoEsperado
        const egreso = data.egreso;

        if (montoContado == '') {
            showToast('Debes ingresar el monto contado', 'error')
            return
        }

        const formData = new FormData();
        formData.append('monto_cierre', montoContado);
        formData.append('saldo_esperado', saldoEsperado);
        formData.append('diferencia', diferencia);
        formData.append('observaciones', observaciones);
        formData.append('egreso', egreso);

        try {
            const res = await fetch('/caja', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            });

            const data = await res.json();
            if (!res.ok) {
                throw data;
            }
            document.getElementById('modalCierreCaja').classList.add('hidden');
            showToast('Caja Cerrada Correctamente');
            limpiarUI();
            document.getElementById('modal-carga').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('modal-carga').classList.add('hidden');
                window.location.href = '/caja';
            }, 900);
        } catch (err) {
            console.log(err)
            showToast(`${err.error}`, 'error')
        }
    });
}

const selectPersonal = document.getElementById('personales');

if (selectPersonal) {
    selectPersonal.addEventListener('change', async (e) => {
        const datosPersonal = document.getElementById('datos-personal');

        try {
            const res = await fetch(`/api/user/${e.target.value}`);
            const data = await res.json();
            if (!res.ok) {
                throw data;
            }
            let fecha = '';
            let fechaFormateada = '';
            if (data.data.user) {
                fecha = new Date(data.data.created_at);
                fechaFormateada = fecha.toLocaleString('es-PY', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }).replace(',', ' -');
            }

            const nombre = data.data.name ?? data.data.user.name;
            const salario = data.data.salario ?? data.data.user.salario;
            const restante = data.data.restante ?? data.data.salario;
            datosPersonal.classList.remove('hidden');
            datosPersonal.innerHTML = `
                        <p class="font-medium">${nombre}</p>
                        <p class="text-sm"><span class="data-personal font-semibold">Salario:</span> Gs. ${salario.toLocaleString('es-PY')}</p>
                        <p class="text-sm"><span class="data-personal font-semibold">Restante:</span> Gs. ${restante.toLocaleString('es-PY')}</p>
                        <p class="text-sm"><span class="data-personal font-semibold">Adelanto:</span> ${fechaFormateada}</p>`;
        } catch (err) {
            console.log(err)
        }
    });
}

if (document.getElementById('max-cajas-form')) {
    document.getElementById('max-cajas-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const valor = document.getElementById('valor').value;
        const id = document.getElementById('cong-id').value;

        try {
            const formData = new FormData();
            formData.append('valor', valor);
            const res = await fetch(`/api/conf/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData
            });
            const data = await res.json();
            if (!res.ok) {
                throw data
            }
            console.log(data)
            showToast('Cantidad máxima de cajas cambiado');
        } catch (err) {
            console.log(err)
        }
    })
}


if($('#agregar-productos')){
    $el('#agregar-productos', 'click', () => {
        const tabla = $('#datos-tabla-productos');
        const datosDer = $('#datos-derecha');
        
        tabla.classList.remove('hidden');
        datosDer.classList.add('hidden');
    });
}

if($('#cerrar-tabla-productos')){
    $el('#cerrar-tabla-productos', 'click', () => {
        const tabla = $('#datos-tabla-productos');
        const datosDer = $('#datos-derecha');
        
        datosDer.classList.remove('hidden');
        tabla.classList.add('hidden');
    })
}

// Función para pre-llenar datos del cliente cuando viene de un servicio
function preLlenarClienteServicio() {
    const servicioCliente = sessionStorage.getItem('servicioCliente');
    
    if (servicioCliente) {
        const cliente = JSON.parse(servicioCliente);
        
        const inputRuc = document.getElementById('i-ruc-ci');
        const inputRazon = document.getElementById('i-nombre-razon');
        
        if (inputRuc && cliente.ruc_ci) {
            inputRuc.value = cliente.ruc_ci;
        }
        
        if (inputRazon && cliente.razon_social) {
            inputRazon.value = cliente.razon_social;
        }
    }
}

// Verificar si viene de un servicio cuando se abre el modal de ventas
document.addEventListener('DOMContentLoaded', () => {
    // Observar cuando el modal de ventas se hace visible
    const modalVentas = document.getElementById('modal-ventas');
    if (modalVentas) {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (!modalVentas.classList.contains('hidden')) {
                        // El modal se ha abierto, verificar si viene de un servicio
                        const fromServicio = sessionStorage.getItem('fromServicio');
                        if (fromServicio === 'true') {
                            preLlenarClienteServicio();
                        }
                    }
                }
            });
        });
        
        observer.observe(modalVentas, { attributes: true });
    }
});