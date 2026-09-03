<?php

require '../../includes/app.php';
estaAutenticado();

$errores = [];

// Valores por defecto
$nombre = '';
$precio = '';
$precio_original = '';
$descripcion = '';
$categoria = '';
$categoria_id = '';
$nueva_categoria = '';
$stock = '';
$caracteristicas = '';

// Obtener categorías
$categorias = mysqli_query($db, "SELECT * FROM categorias ORDER BY nombre ASC");

// =========================================================
// 🔒 LÍMITE DE PRODUCTOS (evita sobrecargar el hosting compartido)
// =========================================================
$totalProductos = 0;
$resTotal = mysqli_query($db, "SELECT COUNT(*) AS total FROM productos");
if ($resTotal) {
    $totalProductos = (int) mysqli_fetch_assoc($resTotal)['total'];
}

$limiteAlcanzado = $totalProductos >= LIMITE_PRODUCTOS;

if ($limiteAlcanzado) {
    $errores[] = "Has alcanzado el límite máximo de " . LIMITE_PRODUCTOS . " productos. Elimina algún producto para poder agregar uno nuevo.";
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && !$limiteAlcanzado) {

    $nombre = mysqli_real_escape_string($db, trim($_POST['nombre'] ?? ''));
    $precio = mysqli_real_escape_string($db, trim($_POST['precio'] ?? ''));
    $precio_original = mysqli_real_escape_string($db, trim($_POST['precio_original'] ?? ''));
    $descripcion = mysqli_real_escape_string($db, trim($_POST['descripcion'] ?? ''));
    $categoria = mysqli_real_escape_string($db, $_POST['categoria'] ?? '');
    $categoria_id = $_POST['categoria_id'] ?? '';
    $nueva_categoria = mysqli_real_escape_string($db, trim($_POST['nueva_categoria'] ?? ''));
    $stock = mysqli_real_escape_string($db, trim($_POST['stock'] ?? ''));
    $caracteristicas = mysqli_real_escape_string($db, trim($_POST['caracteristicas'] ?? ''));

    $imagenPrincipal = $_FILES['imagen_principal'];
    $imagenes = $_FILES['imagenes'];

    // Validaciones
    if($nombre === '') $errores[] = "El nombre es obligatorio";

    // 🔥 El precio es OPCIONAL. Si se pone, debe ser numérico y mayor a 0.
    if($precio_original !== '' && (!is_numeric($precio_original) || $precio_original <= 0)) {
        $errores[] = "El precio original debe ser un número válido";
    }

    // Si pusieron precio de oferta, debe ser numérico y mayor a 0
    if($precio !== '' && (!is_numeric($precio) || $precio <= 0)) {
        $errores[] = "El precio de oferta debe ser un número válido";
    }

    // No tiene sentido una oferta sin precio original
    if($precio !== '' && $precio_original === '') {
        $errores[] = "Si ingresas un precio de oferta, también debes ingresar el precio original";
    }

    if($descripcion === '') $errores[] = "La descripción es obligatoria";
    if(!$imagenPrincipal['name']) $errores[] = "La imagen principal es obligatoria";

    // =====================================================
    // 🔥 CATEGORÍA: puede venir seleccionada, o puede pedirse
    // crear una categoría nueva en el momento ("nueva").
    // =====================================================
    if($categoria_id === 'nueva') {

        if($nueva_categoria === '') {
            $errores[] = "Escribe el nombre de la nueva categoría";
        } else {

            // Evitar duplicados (misma categoría con distinto texto/mayúsculas)
            $queryExiste = "SELECT id FROM categorias WHERE nombre = '$nueva_categoria' LIMIT 1";
            $resExiste = mysqli_query($db, $queryExiste);

            if($resExiste && mysqli_num_rows($resExiste) > 0) {
                $categoria_id = mysqli_fetch_assoc($resExiste)['id'];
            } elseif(empty($errores)) {
                mysqli_query($db, "INSERT INTO categorias (nombre) VALUES ('$nueva_categoria')");
                $categoria_id = mysqli_insert_id($db);
            }
        }

    } elseif(!$categoria_id) {
        $errores[] = "La categoría es obligatoria";
    }

    // 🔥 Si el precio queda en blanco, se guarda como NULL (no como 0)
    // para no confundirlo con "precio válido = 0" cuando se edite después.
    $precioSql = ($precio === '') ? 'NULL' : "'$precio'";
    $precioOriginalSql = ($precio_original === '') ? 'NULL' : "'$precio_original'";

    if(empty($errores)) {

        $carpetaImagenes = '../../imagenes/';
        if(!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        }

        // 🔥 IMAGEN PRINCIPAL
        $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

        move_uploaded_file(
            $imagenPrincipal['tmp_name'],
            $carpetaImagenes . $nombreImagen
        );

        // 🔥 INSERT PRODUCTO
        $query = "INSERT INTO productos 
        (nombre, precio, precio_original, imagen, descripcion, categoria, categoria_id, stock, caracteristicas, creado)
        VALUES 
        ('$nombre', $precioSql, $precioOriginalSql, '$nombreImagen', '$descripcion', '$categoria', '$categoria_id', '$stock', '$caracteristicas', NOW())";

        $resultado = mysqli_query($db, $query);

        if($resultado) {

            $producto_id = mysqli_insert_id($db);

            // 🔥 GALERÍA MÚLTIPLE
            if(!empty($imagenes['name'][0])) {

                for($i = 0; $i < count($imagenes['name']); $i++) {

                    if($imagenes['name'][$i]) {

                        $nombreImg = md5(uniqid(rand(), true)) . ".jpg";

                        move_uploaded_file(
                            $imagenes['tmp_name'][$i],
                            $carpetaImagenes . $nombreImg
                        );

                        $queryImg = "INSERT INTO producto_imagenes (producto_id, imagen)
                                     VALUES ($producto_id, '$nombreImg')";

                        mysqli_query($db, $queryImg);
                    }
                }
            }

            header('Location: /admin/');
            exit;
        } else {
            $errores[] = "Ocurrió un error al guardar el producto. Intenta nuevamente.";
        }
    }
}

incluirTemplate('header');
?>

<main class="contenedor admin-form">

    <h1>Crear Producto</h1>

    <p class="contador-limite">
        Productos registrados: <strong><?php echo $totalProductos; ?> / <?php echo LIMITE_PRODUCTOS; ?></strong>
    </p>

    <?php foreach($errores as $error): ?>
        <p class="alerta-error"><?php echo $error; ?></p>
    <?php endforeach; ?>

    <?php if(!$limiteAlcanzado): ?>

    <form method="POST" enctype="multipart/form-data" class="form-admin">

        <fieldset>
            <legend>Información General</legend>

            <div class="campo">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?php echo $nombre; ?>">
            </div>

            <div class="campo-grid">

                <div class="campo">
                    <label>Precio Original</label>
                    <input type="number" step="0.01" name="precio_original" value="<?php echo $precio_original; ?>">
                </div>

                <div class="campo">
                    <label>Precio Oferta (opcional)</label>
                    <input type="number" step="0.01" name="precio" value="<?php echo $precio; ?>">
                </div>

            </div>

            <!-- 🔥 IMAGEN PRINCIPAL -->
            <div class="campo">
                <label>Imagen Principal</label>
                <input type="file" id="inputPrincipal" name="imagen_principal" accept="image/jpeg, image/png">

                <!-- preview -->
                <div id="previewPrincipal"></div>
            </div>

            <!-- 🔥 GALERÍA -->
            <div class="campo">
                <label>Imágenes adicionales (galería)</label>
                <input type="file" name="imagenes[]" id="inputGaleria" multiple accept="image/jpeg, image/png">

                <!-- preview -->
                <div id="previewGaleria" class="preview-galeria"></div>
            </div>

            <div class="campo">
                <label>Descripción</label>
                <textarea name="descripcion"><?php echo $descripcion; ?></textarea>
            </div>

        </fieldset>

        <fieldset>
            <legend>Características</legend>

            <div class="campo">
                <textarea name="caracteristicas"><?php echo $caracteristicas; ?></textarea>
            </div>

        </fieldset>

        <fieldset>
            <legend>Extras</legend>

            <div class="campo-grid">

                <div class="campo">
                    <label>Categoría</label>
                    <select name="categoria_id" id="selectCategoria" required>
                        <option value="">-- Seleccionar --</option>

                        <?php mysqli_data_seek($categorias, 0); ?>
                        <?php while($cat = mysqli_fetch_assoc($categorias)): ?>
                            <option value="<?php echo $cat['id']; ?>"
                                <?php echo ($cat['id'] == $categoria_id) ? 'selected' : ''; ?>>
                                <?php echo $cat['nombre']; ?>
                            </option>
                        <?php endwhile; ?>

                        <option value="nueva" <?php echo ($categoria_id === 'nueva') ? 'selected' : ''; ?>>
                            + Crear nueva categoría...
                        </option>
                    </select>
                </div>

                <div class="campo">
                    <label>Stock</label>
                    <input type="number" name="stock" value="<?php echo $stock; ?>">
                </div>

            </div>

            <!-- 🔥 CAMPO PARA NUEVA CATEGORÍA (se muestra solo si se elige "+ Crear nueva categoría...") -->
            <div class="campo" id="campoNuevaCategoria" style="<?php echo ($categoria_id === 'nueva') ? '' : 'display:none;'; ?>">
                <label>Nombre de la nueva categoría</label>
                <input type="text" name="nueva_categoria" id="inputNuevaCategoria" value="<?php echo $nueva_categoria; ?>" placeholder="Ej: Suplementos y Colágeno">
            </div>

            <input type="hidden" name="categoria" value="<?php echo $categoria; ?>">

        </fieldset>

        <input type="submit" value="Crear Producto" class="boton boton-full">

    </form>

    <?php else: ?>

        <p>Elimina algún producto existente desde el <a href="/admin/">panel de administración</a> para poder agregar uno nuevo.</p>

    <?php endif; ?>

</main>

<!-- 🔥 Mostrar/ocultar el campo de nueva categoría según la selección -->
<script>
    (function() {
        var select = document.getElementById('selectCategoria');
        var campo = document.getElementById('campoNuevaCategoria');

        if (select && campo) {
            select.addEventListener('change', function() {
                campo.style.display = (select.value === 'nueva') ? 'block' : 'none';
            });
        }
    })();
</script>

<?php incluirTemplate('footer'); ?>
