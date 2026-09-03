<?php

require '../../includes/app.php';
estaAutenticado(); // PROTECCIÓN

$errores = [];
$mensaje = '';

// =========================================================
// 🔥 CREAR CATEGORÍA
// =========================================================
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {

    $nombre = mysqli_real_escape_string($db, trim($_POST['nombre'] ?? ''));

    if($nombre === '') {
        $errores[] = "El nombre de la categoría es obligatorio";
    } else {

        $queryExiste = "SELECT id FROM categorias WHERE nombre = '$nombre' LIMIT 1";
        $resExiste = mysqli_query($db, $queryExiste);

        if($resExiste && mysqli_num_rows($resExiste) > 0) {
            $errores[] = "Ya existe una categoría con ese nombre";
        } else {
            mysqli_query($db, "INSERT INTO categorias (nombre) VALUES ('$nombre')");
            header('Location: /admin/categorias/');
            exit;
        }
    }
}

// =========================================================
// 🔥 EDITAR CATEGORÍA (nombre)
// =========================================================
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'editar') {

    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    $nombre = mysqli_real_escape_string($db, trim($_POST['nombre'] ?? ''));

    if($id && $nombre !== '') {
        mysqli_query($db, "UPDATE categorias SET nombre = '$nombre' WHERE id = $id");
    }

    header('Location: /admin/categorias/');
    exit;
}

// =========================================================
// 🔒 ELIMINAR CATEGORÍA
// Solo se permite si NO tiene productos asociados, para no dejar
// productos huérfanos y evitar borrados accidentales masivos.
// =========================================================
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {

    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);

    if($id) {

        $queryCount = "SELECT COUNT(*) AS total FROM productos WHERE categoria_id = $id";
        $resCount = mysqli_query($db, $queryCount);
        $totalProdCat = (int) mysqli_fetch_assoc($resCount)['total'];

        if($totalProdCat > 0) {
            $errores[] = "No se puede eliminar: hay $totalProdCat producto(s) usando esta categoría. Cámbialos de categoría o elimínalos primero.";
        } else {
            mysqli_query($db, "DELETE FROM categorias WHERE id = $id");
            header('Location: /admin/categorias/');
            exit;
        }
    }
}

// =========================================================
// 🔥 LISTADO DE CATEGORÍAS + CANTIDAD DE PRODUCTOS
// =========================================================
$query = "SELECT categorias.*, COUNT(productos.id) AS total_productos
          FROM categorias
          LEFT JOIN productos ON productos.categoria_id = categorias.id
          GROUP BY categorias.id
          ORDER BY categorias.nombre ASC";

$resultado = mysqli_query($db, $query);

incluirTemplate('header');
?>

<main class="contenedor admin-panel">

    <div class="admin-header">
        <h1>Categorías</h1>

        <div class="admin-acciones">
            <a href="/admin/" class="boton boton-secundario-admin">
                ← Volver a Productos
            </a>
        </div>
    </div>

    <?php foreach($errores as $error): ?>
        <p class="alerta-error"><?php echo $error; ?></p>
    <?php endforeach; ?>

    <!-- 🔥 FORMULARIO PARA CREAR CATEGORÍA -->
    <form method="POST" class="form-admin form-categoria-nueva">
        <input type="hidden" name="accion" value="crear">

        <div class="campo-grid">
            <div class="campo">
                <label>Nueva categoría</label>
                <input type="text" name="nombre" placeholder="Ej: Suplementos y Colágeno" required>
            </div>
        </div>

        <input type="submit" value="+ Crear Categoría" class="boton">
    </form>

    <!-- 🔥 TABLA DE CATEGORÍAS -->
    <div class="tabla-responsive">
        <table class="tabla-admin">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Productos</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php while($cat = mysqli_fetch_assoc($resultado)): ?>
                <tr>
                    <td><?php echo $cat['id']; ?></td>

                    <td>
                        <!-- 🔥 Editar nombre en línea -->
                        <form method="POST" class="form-editar-categoria">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                            <input type="text" name="nombre" value="<?php echo htmlspecialchars($cat['nombre']); ?>">
                            <button type="submit" class="btn-editar">Guardar</button>
                        </form>
                    </td>

                    <td><?php echo $cat['total_productos']; ?></td>

                    <td class="acciones-admin">
                        <form method="POST" onsubmit="return confirm('¿Eliminar esta categoría?')">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                            <input type="submit" value="Eliminar" class="btn-eliminar"
                                <?php echo ($cat['total_productos'] > 0) ? 'disabled title="Tiene productos asociados"' : ''; ?>>
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
