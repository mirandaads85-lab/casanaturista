<?php

require 'includes/app.php';

$busqueda = $_GET['busqueda'] ?? '';
$busqueda = trim($busqueda);

$productos = [];

if($busqueda !== '') {

    $busqueda = mysqli_real_escape_string($db, $busqueda);

    $query = "SELECT * FROM productos 
              WHERE nombre LIKE '%${busqueda}%' 
              OR descripcion LIKE '%${busqueda}%'";

    $resultado = mysqli_query($db, $query);

    while($producto = mysqli_fetch_assoc($resultado)) {
        $productos[] = $producto;
    }
}

incluirTemplate('header');
?>

<main class="contenedor">
    <h1>Resultados para: "<?php echo htmlspecialchars($busqueda); ?>"</h1>

    <?php if(empty($productos)): ?>
        <p>No se encontraron resultados</p>
    <?php else: ?>

        <div class="productos">
            <?php foreach($productos as $producto): ?>

                <div class="producto">

                    <img src="/imagenes/<?php echo $producto['imagen']; ?>">

                    <div class="contenido-producto">
                        <h3><?php echo $producto['nombre']; ?></h3>
                        <p class="descripcion"><?php echo $producto['descripcion']; ?></p>

                        <?php
                            $tieneOferta = !empty($producto['precio'])
                                && $producto['precio'] > 0
                                && !empty($producto['precio_original'])
                                && $producto['precio_original'] > 0
                                && $producto['precio'] < $producto['precio_original'];

                            $tieneAlgunPrecio = !empty($producto['precio_original']) && $producto['precio_original'] > 0;
                        ?>

                        <?php if($tieneOferta): ?>

                            <p class="precio-card">
                                <span class="precio-original">S/.<?php echo $producto['precio_original']; ?></span>
                                <span class="precio">S/.<?php echo $producto['precio']; ?></span>
                            </p>

                        <?php elseif($tieneAlgunPrecio): ?>

                            <p class="precio">S/.<?php echo $producto['precio_original']; ?></p>

                        <?php else: ?>

                            <p class="precio-consultar">consultar precio</p>

                        <?php endif; ?>

                        <a href="/producto.php?id=<?php echo $producto['id']; ?>" class="boton">
                            Ver Producto
                        </a>
                    </div>

                </div>

            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</main>

<?php
incluirTemplate('footer');