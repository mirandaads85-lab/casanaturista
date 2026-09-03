<?php

require 'includes/app.php';

// 🔥 CONSULTAR PRODUCTOS (lo tuyo, NO se toca)
$query = "SELECT * FROM productos ORDER BY creado DESC";
$resultado = mysqli_query($db, $query);

// 🔥 MARCAS DESTACADAS
$queryMarcas = "
SELECT m.posicion, p.*
FROM marcas_destacadas m
JOIN productos p ON m.producto_id = p.id
";
// 🔥 SLIDER DINÁMICO
$querySlider = "SELECT * FROM sliders ORDER BY orden ASC";
$resultadoSlider = mysqli_query($db, $querySlider);

$resultadoMarcas = mysqli_query($db, $queryMarcas);

$marcas = [];

while($row = mysqli_fetch_assoc($resultadoMarcas)) {
    $marcas[$row['posicion']] = $row;
}

// =========================================================
// 🔥 REEL DE OFERTAS (productos con precio de oferta activo)
// Consulta liviana y acotada (LIMIT) para no sobrecargar el hosting.
// Solo entra a la lista si precio_original y precio (oferta) son
// válidos y precio < precio_original.
// =========================================================
$queryOfertas = "SELECT * FROM productos 
                  WHERE precio IS NOT NULL AND precio > 0
                  AND precio_original IS NOT NULL AND precio_original > 0
                  AND precio < precio_original
                  ORDER BY creado DESC
                  LIMIT 12";

$resultadoOfertas = mysqli_query($db, $queryOfertas);

$ofertas = [];
if ($resultadoOfertas) {
    while ($of = mysqli_fetch_assoc($resultadoOfertas)) {
        $ofertas[] = $of;
    }
}

incluirTemplate('header');
?>


<main>

   <section class="hero" id="inicio">

    <div class="hero-overlay"></div>

    <div class="hero-content">

     <span class="hero-tag">
            <span class="line"></span>
            100% Natural
        </span>

        <h1>
            SALUD Y BIENESTAR NATURAL
        </h1>

        <p>
            Productos naturistas seleccionados para cuidar tu salud y la de tu familia. Calidad garantizada en cada producto.
        </p>

        <div class="hero-buttons">

            <a href="#productos" class="btn-primary">
                 Ver Productos
            </a>

            <a href="https://api.whatsapp.com/send/?phone=<?php echo SITIO_WHATSAPP; ?>&text=<?php echo urlencode('Hola, quiero hacer una consulta sobre sus productos naturistas.'); ?>" class="btn-secondary" target="_blank">
                Asesoría Gratuita
            </a>

        </div>

    </div>

</section>

<section class="hero-features">

    <div class="feature">

        <svg class="feature-icon" fill="#ffffff" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 612 612" style="enable-background:new 0 0 612 612;" xml:space="preserve">
<g>
	<g>
		<path d="M504.9,395.756c-28.684,0-52.02,23.342-52.02,52.02c0,28.684,23.336,52.02,52.02,52.02c28.678,0,52.02-23.336,52.02-52.02
			C556.92,419.098,533.578,395.756,504.9,395.756z M504.9,463.076c-8.439,0-15.3-6.861-15.3-15.3c0-8.439,6.861-15.3,15.3-15.3
			s15.3,6.861,15.3,15.3C520.2,456.215,513.339,463.076,504.9,463.076z"/>
		<path d="M499.918,179.518H410.04c-6.763,0-12.24,5.484-12.24,12.24v238.68c0,6.756,5.477,12.24,12.24,12.24h12.981
			c6.059,0,11.426-4.364,12.209-10.373c4.804-36.806,34.162-59.633,69.676-59.633s64.872,22.828,69.676,59.633
			c0.783,6.01,6.144,10.373,12.209,10.373h12.968c6.756,0,12.24-5.484,12.24-12.24v-119.34c0-2.876-1.01-5.655-2.852-7.852
			l-99.842-119.34C506.981,181.128,503.541,179.518,499.918,179.518z M422.28,277.438v-61.2c0-6.756,5.477-12.24,12.24-12.24h53.917
			c3.629,0,7.075,1.616,9.4,4.406l50.998,61.2c6.64,7.974,0.973,20.074-9.406,20.074H434.52
			C427.757,289.678,422.28,284.201,422.28,277.438z"/>
		<path d="M12.24,442.684h31.341c6.059,0,11.426-4.364,12.209-10.373c4.804-36.806,34.162-59.633,69.676-59.633
			s64.872,22.828,69.676,59.633c0.783,6.01,6.144,10.373,12.209,10.373H361.08c6.757,0,12.24-5.484,12.24-12.24v-306
			c0-6.756-5.484-12.24-12.24-12.24H12.24c-6.763,0-12.24,5.484-12.24,12.24v306C0,437.201,5.477,442.684,12.24,442.684z"/>
		<path d="M125.46,395.756c-28.684,0-52.02,23.342-52.02,52.02c0,28.684,23.336,52.02,52.02,52.02
			c28.678,0,52.02-23.336,52.02-52.02C177.48,419.098,154.138,395.756,125.46,395.756z M125.46,463.076
			c-8.439,0-15.3-6.861-15.3-15.3c0-8.439,6.861-15.3,15.3-15.3s15.3,6.861,15.3,15.3
			C140.76,456.215,133.899,463.076,125.46,463.076z"/>
	</g>
</g>
</svg>

        <div>
            <h4>Envíos Nacionales</h4>
            <p>Entrega segura a todo el Perú.</p>
        </div>

    </div>

    <div class="feature">

        <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15l-3.5 2 1-4-3-3.5 4.2-.5L12 5l1.8 4 4.2.5-3 3.5 1 4z"/>
        </svg>

        <div>
            <h4>100% Naturales</h4>
            <p>Productos seleccionados y de calidad.</p>
        </div>

    </div>

    <div class="feature">

        <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M18 10c0-3.3-2.7-6-6-6s-6 2.7-6 6v4l-2 2v1h16v-1l-2-2v-4z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 18a3 3 0 006 0"/>
        </svg>

        <div>
            <h4>Asesoría Especializada</h4>
            <p>Te orientamos en tu consulta.</p>
        </div>

    </div>

</section>

<div class="section-divider"></div>

<?php if(!empty($ofertas)): ?>

    <!-- =========================================================
         🔥 REEL DE OFERTAS
         Carrusel automático, responsive y liviano (solo CSS, sin
         librerías externas). Se pausa al pasar el mouse.
    ========================================================== -->
    <section class="contenedor cont_ofertas" id="ofertas">

        <h2>Ofertas del Día</h2>

        <?php
            // 🔥 velocidad constante sin importar cuántas ofertas haya
            // (mínimo 15s, ~3.5s por tarjeta)
            $duracionReel = max(15, count($ofertas) * 3.5);
        ?>

        <div class="reel-ofertas">
            <div class="reel-ofertas-track" style="animation-duration: <?php echo $duracionReel; ?>s;">

                <?php
                    // 🔥 se duplica la lista una sola vez para lograr
                    // el efecto de bucle infinito sin JavaScript
                    $vueltas = array_merge($ofertas, $ofertas);

                    foreach($vueltas as $of):

                        $descuentoOf = 100 - (($of['precio'] / $of['precio_original']) * 100);
                ?>
                    <div class="card-oferta">

                        <div class="card-oferta-imagen">
                            <img src="/imagenes/<?php echo $of['imagen']; ?>" alt="<?php echo htmlspecialchars($of['nombre']); ?>" loading="lazy">
                            <span class="badge-oferta-card">-<?php echo round($descuentoOf); ?>%</span>
                        </div>

                        <div class="card-oferta-info">

                            <h3><?php echo htmlspecialchars($of['nombre']); ?></h3>

                            <div class="precio-card">
                                <span class="pantes">Antes S/.</span>
                                <span class="precio-original"><?php echo $of['precio_original']; ?></span>
                                <span class="precio">Oferta S/.<?php echo $of['precio']; ?></span>
                            </div>

                            <div class="acciones-producto">

                                <a href="/producto.php?id=<?php echo $of['id']; ?>" class="botonp">
                                    Ver
                                </a>

                                <form class="form-agregar">
                                    <input type="hidden" name="id" value="<?php echo $of['id']; ?>">
                                    <button type="submit" class="btn-comprar-card">Comprar</button>
                                </form>

                            </div>

                        </div>

                    </div>
                <?php endforeach; ?>

            </div>
        </div>

    </section>

    <div class="section-divider"></div>

<?php endif; ?>

    <section class="contenedor cont_productos" id="productos">
        <h2>Nuestros Productos</h2>

        <div class="productos">
            <?php include 'includes/templates/productos.php'; ?>
        </div>


    </section>
    
</main>

<?php
incluirTemplate('footer');
