<?php

require '../includes/app.php';
estaAutenticado(); // PROTECCIÓN

// 🔥 ELIMINAR PRODUCTO
if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if($id) {

        // Obtener imagen
        $query = "SELECT imagen FROM productos WHERE id = ${id}";
        $resultado = mysqli_query($db, $query);
        $producto = mysqli_fetch_assoc($resultado);

        // Eliminar imagen
        if($producto && file_exists('../imagenes/' . $producto['imagen'])) {
            unlink('../imagenes/' . $producto['imagen']);
        }

        // Eliminar registro
        $query = "DELETE FROM productos WHERE id = ${id}";
        mysqli_query($db, $query);

        header('Location: /admin/');
        exit;
    }
}

// 🔥 CONSULTAR PRODUCTOS
$query = "SELECT productos.*, categorias.nombre AS categoria_nombre
FROM productos
LEFT JOIN categorias ON productos.categoria_id = categorias.id
ORDER BY productos.creado DESC";

$resultado = mysqli_query($db, $query);

// 🔒 CONTADOR PARA EL LÍMITE DE PRODUCTOS
$totalProductos = mysqli_num_rows($resultado);
$limiteAlcanzado = $totalProductos >= LIMITE_PRODUCTOS;

incluirTemplate('header');
?>

<main class="contenedor admin-panel">

    <!-- 🔥 HEADER -->
    <div class="admin-header">
        <h1>Administrador de Productos</h1>

        <div class="admin-acciones">

            <a href="/admin/categorias/" class="boton boton-secundario-admin">
                Categorías
            </a>

            <?php if(!$limiteAlcanzado): ?>
                <a href="/admin/productos/crear.php" class="boton">
                    +Agregar
                </a>
            <?php endif; ?>

        </div>
    </div>

    <!-- 🔒 AVISO DE LÍMITE DE PRODUCTOS -->
    <p class="contador-limite <?php echo $limiteAlcanzado ? 'limite-lleno' : ''; ?>">
        Productos registrados: <strong><?php echo $totalProductos; ?> / <?php echo LIMITE_PRODUCTOS; ?></strong>

        <?php if($limiteAlcanzado): ?>
            — Has alcanzado el límite máximo. Elimina algún producto para poder agregar uno nuevo.
        <?php endif; ?>
    </p>

    <!-- 🔥 TABLA -->
    <div class="tabla-responsive">
        <table class="tabla-admin">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php while($producto = mysqli_fetch_assoc($resultado)): ?>
                <tr>

                    <td><?php echo $producto['id']; ?></td>

                    <td>
                        <img class="img-admin" src="/imagenes/<?php echo $producto['imagen']; ?>">
                    </td>

                    <td class="nombre-prod"><?php echo $producto['nombre']; ?></td>

                    <!-- 🔥 PRECIO: respeta la misma lógica que el resto del sitio -->
                    <td class="precio-admin">
                        <?php if(empty($producto['precio_original']) || $producto['precio_original'] <= 0): ?>
                            Consultar precio
                        <?php elseif(!empty($producto['precio']) && $producto['precio'] > 0 && $producto['precio'] < $producto['precio_original']): ?>
                            <span class="precio-tachado">S/. <?php echo $producto['precio_original']; ?></span>
                            S/. <?php echo $producto['precio']; ?>
                        <?php else: ?>
                            S/. <?php echo $producto['precio_original']; ?>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php echo $producto['categoria_nombre'] ?? 'Sin categoría'; ?>
                    </td>

                    <td>
                        <span class="stock <?php echo $producto['stock'] <= 5 ? 'bajo' : ''; ?>">
                            <?php echo $producto['stock']; ?>
                        </span>
                    </td>

                    <td class="acciones-admin">

                        <a href="/admin/productos/actualizar.php?id=<?php echo $producto['id']; ?>" class="btn-editar">
                            Editar
                        </a>

                        <form method="POST" onsubmit="return confirm('¿Eliminar este producto?')">
                            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                            <input type="submit" value="Eliminar" class="btn-eliminar">
                        </form>

                    </td>

                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</main>

<?php
incluirTemplate('footer');
