<?php

require 'includes/app.php';

// Validar ID
$id = $_GET['id'] ?? null;
$id = filter_var($id, FILTER_VALIDATE_INT);

if(!$id) {
    header('Location: /casanaturista');
    exit;
}

// Consultar categoría
$queryCat = "SELECT * FROM categorias WHERE id = ${id}";
$resCat = mysqli_query($db, $queryCat);
$categoria = mysqli_fetch_assoc($resCat);

if(!$categoria) {
    header('Location: /casanaturista');
    exit;
}

// Consultar productos de esa categoría
$query = "SELECT * FROM productos WHERE categoria_id = ${id}";
$resultado = mysqli_query($db, $query);

incluirTemplate('header');
?>

<main class="contenedor">
    <h1><?php echo $categoria['nombre']; ?></h1>

    <?php if(mysqli_num_rows($resultado) === 0): ?>
        <p>No hay productos en esta categoría</p>
    <?php else: ?>

        <div class="productos">
            <?php include 'includes/templates/productos.php'; ?>
        </div>

    <?php endif; ?>

</main>

<?php
incluirTemplate('footer');