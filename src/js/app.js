document.addEventListener('DOMContentLoaded', () => {

   // =========================
// PREVIEW IMAGEN PRINCIPAL
// =========================
const inputPrincipal = document.getElementById('inputPrincipal');
const previewPrincipal = document.getElementById('previewPrincipal');

if (inputPrincipal) {
    inputPrincipal.addEventListener('change', () => {

        previewPrincipal.innerHTML = '';

        const file = inputPrincipal.files[0];

        if (file) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.width = '120px';

            previewPrincipal.appendChild(img);
        }
    });
}


// =========================
// PREVIEW GALERÍA
// =========================
const inputGaleria = document.getElementById('inputGaleria');
const previewGaleria = document.getElementById('previewGaleria');

if (inputGaleria) {
    inputGaleria.addEventListener('change', () => {

        previewGaleria.innerHTML = '';

        Array.from(inputGaleria.files).forEach(file => {

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);

            previewGaleria.appendChild(img);
        });
    });
}

    // =========================
    // SCROLL HEADER
    // =========================
    const header = document.querySelector('.header');

    if (header) {
        window.addEventListener('scroll', () => {

            if (window.scrollY > 10) {
                header.style.boxShadow = "0 5px 15px rgba(0,0,0,0.15)";
            } else {
                header.style.boxShadow = "none";
            }

        });
    }

    // =========================
    // FILTROS RESPONSIVE
    // =========================
    const btnFiltros = document.getElementById('btnFiltros');
    const panelFiltros = document.getElementById('panelFiltros');

    if (btnFiltros && panelFiltros) {
        btnFiltros.addEventListener('click', () => {
            panelFiltros.classList.toggle('activo');
        });
    }


    

});