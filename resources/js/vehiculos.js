
function toggleEstadisticasVehiculos() {
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

function abrirModalNuevoVehiculoVehiculos() {
    document.getElementById('modal-nuevo-vehiculo').classList.remove('hidden');
    document.getElementById('modal-nuevo-vehiculo').classList.add('flex');
}

function cerrarModalNuevoVehiculoVehiculos() {
    document.getElementById('modal-nuevo-vehiculo').classList.add('hidden');
    document.getElementById('modal-nuevo-vehiculo').classList.remove('flex');
}

function abrirModalEditarVehiculoVehiculos(vehiculo) {
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

function cerrarModalEditarVehiculoVehiculos() {
    document.getElementById('modal-editar-vehiculo').classList.add('hidden');
    document.getElementById('modal-editar-vehiculo').classList.remove('flex');
}

function buscarVehiculos() {
    const search = document.getElementById('input-buscar').value;
    window.location.href = `{{ route('vehiculo.index') }}?search=${encodeURIComponent(search)}`;
}

document.getElementById('input-buscar').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') buscarVehiculos();
});

document.getElementById('form-nuevo-vehiculo').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('vehiculos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showToast(result.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(result.error, 'error');
        }
    } catch (error) {
        showToast('Error al guardar el vehículo', 'error');
    }
});

document.getElementById('form-editar-vehiculo').addEventListener('submit', async function (e) {
    e.preventDefault();
    const id = document.getElementById('edit_vehiculo_id').value;
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(`/vehiculos/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showToast('Vehículo actualizado correctamente', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast(result.error || 'Error al actualizar', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error al procesar la solicitud', 'error');
    }
});