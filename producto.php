<?php

require 'includes/app.php';

// Validar ID
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if(!$id) {
    header('Location: /casanaturista');
    exit;
}

// Consultar producto
$query = "SELECT * FROM productos WHERE id = {$id}";
$resultado = mysqli_query($db, $query);
$producto = mysqli_fetch_assoc($resultado);

// Si no existe producto
if(!$producto) {
    header('Location: /casanaturista');
    exit;
}

// 🔥 CONSULTAR GALERÍA
$queryImgs = "SELECT * FROM producto_imagenes WHERE producto_id = $id";
$resultImgs = mysqli_query($db, $queryImgs);

// 🔥 PRODUCTOS RELACIONADOS (MEJORADO)
if(!empty($producto['categoria_id'])) {

    $queryRelacionados = "SELECT * FROM productos 
    WHERE categoria_id = {$producto['categoria_id']} 
    AND id != {$producto['id']} 
    ORDER BY creado DESC
    LIMIT 8";

} else {

    $queryRelacionados = "SELECT * FROM productos 
    WHERE id != {$producto['id']} 
    ORDER BY creado DESC
    LIMIT 8";
}

$resultRelacionados = mysqli_query($db, $queryRelacionados);

incluirTemplate('header');
?>

<main class="contenedor">

    <div class="detalle-producto">

        <!-- 🔥 GALERÍA -->
        <div class="galeria-producto">

            <img id="imgPrincipal" 
                 src="/imagenes/<?php echo $producto['imagen']; ?>" 
                 alt="imagen producto">

            <div class="miniaturas">

                <!-- Imagen principal -->
                <img src="/imagenes/<?php echo $producto['imagen']; ?>" 
                     onclick="cambiarImagen(this.src)">

                <!-- Imágenes adicionales -->
                <?php while($img = mysqli_fetch_assoc($resultImgs)): ?>
                    <img src="/imagenes/<?php echo $img['imagen']; ?>" 
                         onclick="cambiarImagen(this.src)">
                <?php endwhile; ?>

            </div>

        </div>

        <!-- INFO -->
        <div class="detalle-info">

            <h1><?php echo $producto['nombre']; ?></h1>

            <div class="precio-detalle">

                <?php
                    // 🔥 Hay oferta solo si el precio de oferta existe, es mayor a 0
                    // y además es menor que el precio original (evita división por cero)
                    $tieneOferta = !empty($producto['precio'])
                        && $producto['precio'] > 0
                        && !empty($producto['precio_original'])
                        && $producto['precio_original'] > 0
                        && $producto['precio'] < $producto['precio_original'];
                ?>

                <?php $tieneAlgunPrecio = !empty($producto['precio_original']) && $producto['precio_original'] > 0; ?>

                <?php if($tieneOferta): ?>

                    <!-- 🔥 ORDEN: Antes -> precio tachado -> OFERTA -> precio nuevo -> % descuento -->
                    <span class="label-antes">Antes S/.
                    <span class="precio-original pdet">
                        <?php echo $producto['precio_original']; ?>
                    </span>
                    </span>
                    <span class="badge-oferta-detalle">OFERTA</span>

                    <?php
                        $descuento = 100 - (($producto['precio'] / $producto['precio_original']) * 100);
                    ?>

                    <span class="precio-grande">
                        S/.<?php echo $producto['precio']; ?>
                    </span>

                    <span class="badge-descuento">
                        -<?php echo round($descuento); ?>%
                    </span>

                <?php elseif($tieneAlgunPrecio): ?>

                    <span class="precio-grande">
                        S/.<?php echo $producto['precio_original']; ?>
                    </span>

                <?php else: ?>

                    <span class="precio-consultar">
                        Consultar precio
                    </span>

                <?php endif; ?>

            </div>

            <p><?php echo $producto['descripcion']; ?></p>

            <!-- FICHA TÉCNICA -->
            <?php if(!empty($producto['caracteristicas'])): ?>

                <?php $caracteristicas = explode(',', $producto['caracteristicas']); ?>

                <div class="ficha-tecnica">
                    <h3 class="h3producto">Características</h3>

                    <ul>
                        <?php foreach($caracteristicas as $car): 
                            $partes = explode(':', $car, 2);
                        ?>
                            <li>
                                <?php if(count($partes) === 2): ?>
                                    <strong><?php echo trim($partes[0]); ?>:</strong>
                                    <?php echo trim($partes[1]); ?>
                                <?php else: ?>
                                    <?php echo trim($car); ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            <?php endif; ?>

            <!-- BOTONES -->
            <div class="btnProducto">

                <form class="form-agregar">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    <button class="boton" type="submit">Comprar</button>
                </form>

                <?php 
                    if($tieneOferta) {
                        $precioMensaje = "S/." . $producto['precio'];
                    } elseif($tieneAlgunPrecio) {
                        $precioMensaje = "S/." . $producto['precio_original'];
                    } else {
                        $precioMensaje = "A consultar";
                    }
                    $mensaje = "Hola, quiero comprar:\n";
                    $mensaje .= $producto['nombre'] . "\n";
                    $mensaje .= "Precio: " . $precioMensaje;
                ?>

                <a 
                    href="https://wa.me/<?php echo SITIO_WHATSAPP; ?>?text=<?php echo urlencode($mensaje); ?>" 
                    target="_blank" 
                    class="boton boton-secundario"
                >
                    Comprar por WhatsApp
                </a>

            </div>

        </div>

    </div>

    <!-- 🔥 RELACIONADOS -->
<section class="relacionados">

    <h2>Productos relacionados</h2>

    <?php 
    // 🔥 guardar resultados en array
    $relacionadosArray = [];
    while($rel = mysqli_fetch_assoc($resultRelacionados)) {
        $relacionadosArray[] = $rel;
    }
    ?>

    <div class="reel-productos">
        <div class="reel-track">

            <!-- 🔥 PRIMERA VUELTA -->
            <?php foreach($relacionadosArray as $rel): ?>
                <div class="card-rel">

                    <a href="/producto.php?id=<?php echo $rel['id']; ?>">
                        <img src="/imagenes/<?php echo $rel['imagen']; ?>" alt="">
                    </a>

                    <p><?php echo $rel['nombre']; ?></p>

                    <?php
                        $relTieneOferta = !empty($rel['precio'])
                            && $rel['precio'] > 0
                            && !empty($rel['precio_original'])
                            && $rel['precio_original'] > 0
                            && $rel['precio'] < $rel['precio_original'];

                        $relTieneAlgunPrecio = !empty($rel['precio_original']) && $rel['precio_original'] > 0;

                        $precioRel = $relTieneOferta ? $rel['precio'] : $rel['precio_original'];
                    ?>

                    <?php if($relTieneOferta || $relTieneAlgunPrecio): ?>
                        <span class="precio">S/.<?php echo $precioRel; ?></span>
                    <?php else: ?>
                        <span class="precio-consultar">Consultar precio</span>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>

        </div>
    </div>

</section>

</main>

<!-- 🔥 SCRIPT SOLO GALERÍA (CORRECTO AQUÍ) -->
<script>
function cambiarImagen(src) {
    document.getElementById('imgPrincipal').src = src;
}
</script>

<?php
incluirTemplate('footer');