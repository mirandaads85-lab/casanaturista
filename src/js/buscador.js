document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('buscadorInput');
    const resultados = document.getElementById('resultadosBusqueda');

    if (!input || !resultados) return;

    let timeout;

    input.addEventListener('keyup', () => {

        clearTimeout(timeout);

        timeout = setTimeout(async () => {

            const query = input.value.trim();

            if (query.length < 2) {
                resultados.style.display = 'none';
                return;
            }

            try {

                const res = await fetch(`/buscar-ajax.php?q=${query}`);
                const data = await res.json();

                resultados.innerHTML = '';

                if (data.length === 0) {
                    resultados.style.display = 'none';
                    return;
                }

                data.forEach(prod => {

                    const item = document.createElement('a');
                    item.href = `/producto.php?id=${prod.id}`;
                    item.classList.add('resultado-item');

                    // 🔥 Si no tiene precio, mostrar "Consultar precio"
                    const tienePrecio = prod.precioFinal !== null && prod.precioFinal > 0;

                    const precioHtml = tienePrecio
                        ? `S/.${prod.precioFinal}`
                        : `<span class="precio-consultar">Consultar precio</span>`;

                    item.innerHTML = `
                        <img src="/imagenes/${prod.imagen}">
                        <div>
                            <span>${prod.nombre}</span>
                            <span>${precioHtml}</span>
                        </div>
                    `;

                    resultados.appendChild(item);
                });

                resultados.style.display = 'block';

            } catch (error) {
                console.log('Error en buscador:', error);
            }

        }, 300); // 🔥 debounce (300ms)

    });

});