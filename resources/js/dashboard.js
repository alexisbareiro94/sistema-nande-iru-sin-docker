/**
 * Dashboard JavaScript
 * Maneja los gráficos y la interactividad del dashboard
 */
import Chart from 'chart.js/auto';

// Variables globales para los gráficos
let formasPagoChart = null;

/**
 * Inicialización del dashboard
 */
document.addEventListener('DOMContentLoaded', async () => {
    if (window.location.pathname === '/' || window.location.pathname === '/dashboard') {
        await initFormasPagoChart();
        initPeriodoFilters();
    }
});

/**
 * Gráfico de formas de pago (Donut Chart)
 */
async function initFormasPagoChart() {
    const ctx = document.getElementById('chart-formas-pago');
    if (!ctx || !window.formasPagoData) return;

    const data = window.formasPagoData;
    const labels = Object.keys(data).map(label => label.charAt(0).toUpperCase() + label.slice(1));
    const valores = Object.values(data).map(d => d.cantidad);
    const montos = Object.values(data).map(d => d.monto);

    const colores = {
        'efectivo': '#10b981',
        'transferencia': '#3b82f6',
        'mixto': '#8b5cf6'
    };

    const backgroundColors = Object.keys(data).map(key => colores[key] || '#6b7280');

    if (formasPagoChart) {
        formasPagoChart.destroy();
    }

    formasPagoChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: valores,
                backgroundColor: backgroundColors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const monto = montos[context.dataIndex];
                            return `${label}: ${value} ventas (Gs. ${monto.toLocaleString('es-PY')})`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Actualizar gráfico de formas de pago con nuevos datos
 */
function updateFormasPagoChart(formasPago) {
    const ctx = document.getElementById('chart-formas-pago');
    if (!ctx) return;

    const labels = Object.keys(formasPago).map(label => label.charAt(0).toUpperCase() + label.slice(1));
    const valores = Object.values(formasPago).map(d => d.cantidad);
    const montos = Object.values(formasPago).map(d => d.monto);

    const colores = {
        'efectivo': '#10b981',
        'transferencia': '#3b82f6',
        'mixto': '#8b5cf6'
    };

    const backgroundColors = Object.keys(formasPago).map(key => colores[key] || '#6b7280');

    if (formasPagoChart) {
        formasPagoChart.destroy();
    }

    formasPagoChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: valores,
                backgroundColor: backgroundColors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const monto = montos[context.dataIndex];
                            return `${label}: ${value} ventas (Gs. ${monto.toLocaleString('es-PY')})`;
                        }
                    }
                }
            }
        }
    });
}

/**
 * Filtros de período
 */
function initPeriodoFilters() {
    const buttons = document.querySelectorAll('.btn-periodo');

    buttons.forEach(btn => {
        btn.addEventListener('click', function () {
            const periodo = this.dataset.periodo;

            // Actualizar estilos de botones
            buttons.forEach(b => {
                b.classList.remove('bg-gray-800', 'text-white');
                b.classList.add('text-gray-600', 'hover:bg-gray-200');
            });
            this.classList.add('bg-gray-800', 'text-white');
            this.classList.remove('text-gray-600', 'hover:bg-gray-200');

            // Cargar nuevos datos
            fetchDashboardData(periodo);
        });
    });
}

/**
 * Obtener datos del dashboard
 */
async function fetchDashboardData(periodo) {
    try {
        showLoading();

        const response = await fetch(`/api/dashboard/stats/${periodo}`);
        const result = await response.json();

        if (result.success) {
            updateResumen(result.datos.resumen);
            updateFormasPagoChart(result.datos.formas_pago);
            updateFormasPagoLeyenda(result.datos.formas_pago);
            updateTopProductos(result.datos.top_productos);
            updateCajerosStats(result.datos.cajeros_stats);
            updateRangoFechas(result.datos.fecha_inicio, result.datos.fecha_fin);
        }
    } catch (error) {
        console.error('Error al cargar datos:', error);
    } finally {
        hideLoading();
    }
}

/**
 * Actualizar rango de fechas
 */
function updateRangoFechas(fechaInicio, fechaFin) {
    const desde = document.getElementById('fecha-desde');
    const hasta = document.getElementById('fecha-hasta');

    if (desde) desde.textContent = fechaInicio;
    if (hasta) hasta.textContent = fechaFin;
}

/**
 * Actualizar cards de resumen
 */
function updateResumen(resumen) {
    const totalIngresos = document.getElementById('total-ingresos');
    const totalEgresos = document.getElementById('total-egresos');
    const balance = document.getElementById('balance');
    const cantidadVentas = document.getElementById('cantidad-ventas');

    if (totalIngresos) totalIngresos.textContent = `Gs. ${resumen.total_ingresos.toLocaleString('es-PY')}`;
    if (totalEgresos) totalEgresos.textContent = `Gs. ${resumen.total_egresos.toLocaleString('es-PY')}`;
    if (balance) balance.textContent = `Gs. ${resumen.balance.toLocaleString('es-PY')}`;
    if (cantidadVentas) cantidadVentas.textContent = resumen.cantidad_ventas;
}

/**
 * Actualizar leyenda de formas de pago
 */
function updateFormasPagoLeyenda(formasPago) {
    const container = document.getElementById('leyenda-formas-pago');
    if (!container) return;

    const colores = {
        'efectivo': 'bg-emerald-500',
        'transferencia': 'bg-blue-500',
        'mixto': 'bg-purple-500'
    };

    let html = '';
    for (const [forma, data] of Object.entries(formasPago)) {
        html += `
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full ${colores[forma] || 'bg-gray-500'}"></span>
                    <span class="capitalize text-gray-700">${forma}</span>
                </div>
                <div class="text-right">
                    <span class="font-semibold text-gray-800">${data.cantidad}</span>
                    <span class="text-gray-500 text-xs ml-1">(Gs. ${data.monto.toLocaleString('es-PY')})</span>
                </div>
            </div>
        `;
    }
    container.innerHTML = html;
}

/**
 * Actualizar tabla de top productos
 */
function updateTopProductos(productos) {
    const tbody = document.getElementById('tabla-top-productos');
    if (!tbody) return;

    if (productos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="py-8 text-center text-gray-500">
                    No hay productos vendidos en este período
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    productos.forEach((producto, index) => {
        const badgeClass = index === 0 ? 'bg-yellow-100 text-yellow-700' :
            index === 1 ? 'bg-gray-200 text-gray-700' :
                index === 2 ? 'bg-amber-100 text-amber-700' :
                    'bg-gray-100 text-gray-600';

        html += `
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                <td class="py-3 px-2">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold ${badgeClass}">
                        ${index + 1}
                    </span>
                </td>
                <td class="py-3 px-2">
                    <span class="font-medium text-gray-800">${producto.nombre}</span>
                </td>
                <td class="py-3 px-2 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ${producto.cantidad} uds
                    </span>
                </td>
                <td class="py-3 px-2 text-right font-semibold text-gray-800">
                    Gs. ${producto.total.toLocaleString('es-PY')}
                </td>
            </tr>
        `;
    });
    tbody.innerHTML = html;
}

/**
 * Actualizar tabla de estadísticas de cajeros
 */
function updateCajerosStats(cajeros) {
    const tbody = document.getElementById('tabla-cajeros');
    if (!tbody) return;

    if (cajeros.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="py-8 text-center text-gray-500">
                    No hay datos de cajeros en este período
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    cajeros.forEach(cajero => {
        const inicial = cajero.nombre.charAt(0).toUpperCase();
        html += `
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                <td class="py-4 px-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center text-white font-bold">
                            ${inicial}
                        </div>
                        <span class="font-medium text-gray-800">${cajero.nombre}</span>
                    </div>
                </td>
                <td class="py-4 px-4 text-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                        ${cajero.cantidad_ventas}
                    </span>
                </td>
                <td class="py-4 px-4 text-right font-bold text-emerald-600">
                    Gs. ${cajero.total_ventas.toLocaleString('es-PY')}
                </td>
                <td class="py-4 px-4 text-right text-gray-700">
                    Gs. ${cajero.promedio_venta.toLocaleString('es-PY')}
                </td>
                <td class="py-4 px-4 text-right text-gray-700">
                    Gs. ${cajero.mayor_venta.toLocaleString('es-PY')}
                </td>
            </tr>
        `;
    });
    tbody.innerHTML = html;
}

/**
 * Mostrar/ocultar loading
 */
function showLoading() {
    document.querySelectorAll('.bg-gradient-to-br').forEach(el => {
        el.style.opacity = '0.7';
    });
}

function hideLoading() {
    document.querySelectorAll('.bg-gradient-to-br').forEach(el => {
        el.style.opacity = '1';
    });
}
