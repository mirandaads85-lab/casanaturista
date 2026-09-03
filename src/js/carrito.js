document.addEventListener('DOMContentLoaded', () => {

    const carritoBtn = document.getElementById('btnCarrito');
    const mini = document.getElementById('miniCarrito');
    const cerrarMini = document.getElementById('cerrarMini');
    const miniContenido = document.getElementById('miniContenido');
    const contador = document.getElementById('contadorCarrito');

    // =========================
    // MINI CARRITO
    // =========================
    if (carritoBtn && mini) {
        carritoBtn.addEventListener('click', e => {
            e.preventDefault();
            mini.classList.add('activo');
            cargarMini();
        });
    }

    if (cerrarMini && mini) {
        cerrarMini.addEventListener('click', () => {
            mini.classList.remove('activo');
        });
    }

    // =========================
    // CARGAR MINI
    // =========================
    async function cargarMini() {
        if (!miniContenido) return;

        const res = await fetch('/mini-carrito.php');
        const html = await res.text();
        miniContenido.innerHTML = html;
    }

    // =========================
    // CONTADOR
    // =========================
    async function actualizarContador() {
        if (!contador) return;

        const res = await fetch('/mini-carrito.php');
        const html = await res.text();

        const temp = document.createElement('div');
        temp.innerHTML = html;

        let total = 0;

        temp.querySelectorAll('.mini-item-cantidad').forEach(el => {
            total += parseInt(el.innerText);
        });

        contador.innerText = `(${total})`;
    }

    // =========================
    // AGREGAR PRODUCTO
    // =========================
    document.querySelectorAll('.form-agregar').forEach(form => {

        form.addEventListener('submit', async e => {
            e.preventDefault();

            const data = new FormData(form);

            try {

                const res = await fetch('/agregar-carrito.php', {
                    method: 'POST',
                    body: data
                });

                const json = await res.json();

                if (json.ok) {

                    const modal = document.getElementById('modalCarrito');
                    const nombre = document.getElementById('productoAgregado');

                    if (modal) modal.classList.add('activo');
                    if (nombre) nombre.innerText = json.nombre;

                    cargarMini();
                    actualizarContador();
                }

            } catch (error) {
                console.log('Error:', error);
            }

        });

    });

    // =========================
    // CERRAR MODAL
    // =========================
    const btnSeguir = document.getElementById('seguirComprando');

    if (btnSeguir) {
        btnSeguir.addEventListener('click', () => {
            const modal = document.getElementById('modalCarrito');
            if (modal) modal.classList.remove('activo');
            actualizarContador();
        });
    }

    // =========================
    // IR AL CARRITO
    // =========================
    const btnIrCarrito = document.querySelector('#modalCarrito a');

    if (btnIrCarrito) {
        btnIrCarrito.addEventListener('click', () => {
            const modal = document.getElementById('modalCarrito');
            if (modal) modal.classList.remove('activo');
        });
    }

    // =========================
    // ELIMINAR PRODUCTO
    // =========================
    document.addEventListener('click', async e => {

        if (e.target.classList.contains('eliminar-item')) {

            const id = e.target.dataset.id;

            await fetch('/eliminar-carrito.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            });

            cargarMini();
            actualizarContador();
        }

    });

});