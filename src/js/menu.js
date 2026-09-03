document.addEventListener('DOMContentLoaded', () => {

    // =========================
    // 🔥 MENÚ HAMBURGUESA
    // =========================
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('menu');

    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            menu.classList.toggle('active');
        });
    }

    // =========================
    // 🔥 MEGA MENÚ CATEGORÍAS
    // =========================
    const btnCategorias = document.getElementById('btnCategorias');
    const menuCategorias = document.getElementById('menuCategorias');

    if (btnCategorias && menuCategorias) {

        btnCategorias.addEventListener('click', () => {
            menuCategorias.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (!menuCategorias.contains(e.target) && !btnCategorias.contains(e.target)) {
                menuCategorias.classList.remove('active');
            }
        });

    }

});