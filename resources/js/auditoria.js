if (document.querySelectorAll('.mostrar-detalles')) {
    const btns = document.querySelectorAll('.mostrar-detalles');
    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            const detalles = document.getElementById(`detalles-${btn.dataset.id}`);
            const icon = document.getElementById(`icon-${btn.dataset.id}`);
            const text = document.getElementById(`text-${btn.dataset.id}`);
            detalles.classList.toggle('hidden');
            icon.classList.toggle('rotate-90');
            text.textContent = icon.classList.contains('rotate-90') ? 'Ocultar detalles del cambio' : 'Ver detalles del cambio';
        });
    });
}