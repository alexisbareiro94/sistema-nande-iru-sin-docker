import { csrfToken } from "./csrf-token.js";

window.toggleEstadisticasVehiculos = function () {
    const panel = document.getElementById('panel-estadisticas');
    const btnText = document.getElementById('btn-estadisticas-text');
    const arrow = document.getElementById('icon-estadisticas-arrow');

    if (panel.classList.contains('hidden')) {
        panel.classList.remove('hidden');
        btnText.textContent = 'Ocultar Estadísticas';
        arrow.style.transform = 'rotate(180deg)';
    } else {
        panel.classList.add('hidden');
        btnText.textContent = 'Ver Estadísticas';
        arrow.style.transform = 'rotate(0deg)';
    }
}

window.abrirModalNuevoVehiculoVehiculos = function () {
    document.getElementById('modal-nuevo-vehiculo').classList.remove('hidden');
    document.getElementById('modal-nuevo-vehiculo').classList.add('flex');
}

window.cerrarModalNuevoVehiculoVehiculos = function () {
    document.getElementById('modal-nuevo-vehiculo').classList.add('hidden');
    document.getElementById('modal-nuevo-vehiculo').classList.remove('flex');
}

window.abrirModalEditarVehiculoVehiculos = function (vehiculo) {
    document.getElementById('edit_vehiculo_id').value = vehiculo.id;
    document.getElementById('edit_patente').value = vehiculo.patente;
    document.getElementById('edit_marca').value = vehiculo.marca;
    document.getElementById('edit_modelo').value = vehiculo.modelo;
    document.getElementById('edit_anio').value = vehiculo.anio || '';
    document.getElementById('edit_color').value = vehiculo.color || '';
    document.getElementById('edit_kilometraje').value = vehiculo.kilometraje || '';

    document.getElementById('edit_cliente_id').value = vehiculo.cliente_id || '';
    document.getElementById('edit_mecanico_id').value = vehiculo.mecanico_id || '';
    document.getElementById('edit_observaciones').value = vehiculo.observaciones || '';

    document.getElementById('modal-editar-vehiculo').classList.remove('hidden');
    document.getElementById('modal-editar-vehiculo').classList.add('flex');
}

window.cerrarModalEditarVehiculoVehiculos = function () {
    document.getElementById('modal-editar-vehiculo').classList.add('hidden');
    document.getElementById('modal-editar-vehiculo').classList.remove('flex');
}


document.addEventListener('DOMContentLoaded', function () {
    const formNuevoVehiculo = document.getElementById('form-nuevo-vehiculo');
    const formEditarVehiculo = document.getElementById('form-editar-vehiculo');

    if (formNuevoVehiculo) {
        formNuevoVehiculo.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('/vehiculos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    window.showToast(result.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    window.showToast(result.error, 'error');
                }
            } catch (error) {
                window.showToast('Error al guardar el vehículo', 'error');
            }
        });
    }

    if (formEditarVehiculo) {
        formEditarVehiculo.addEventListener('submit', async function (e) {
            e.preventDefault();
            const id = document.getElementById('edit_vehiculo_id').value;
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`/vehiculos/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    window.showToast('Vehículo actualizado correctamente', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    window.showToast(result.error || 'Error al actualizar', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                window.showToast('Error al procesar la solicitud', 'error');
            }
        });
    }
});
