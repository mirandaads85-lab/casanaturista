<?php

require '../../includes/app.php';
estaAutenticado();

// Validar ID
$id = $_GET['id'];
$id = filter_var($id, FILTER_VALIDATE_INT);

if(!$id) {
    header('Location: /admin/');
    exit;
}

// 🔥 ELIMINAR IMAGEN DE GALERÍA
if(isset($_GET['eliminar_img'])) {

    $img_id = filter_var($_GET['eliminar_img'], FILTER_VALIDATE_INT);

    if($img_id) {

        // Obtener imagen
        $query = "SELECT imagen FROM producto_imagenes WHERE id = $img_id";
        $res = mysqli_query($db, $query);
        $img = mysqli_fetch_assoc($res);

        if($img) {
            $ruta = '../../imagenes/' . $img['imagen'];

            if(file_exists($ruta)) {
                unlink($ruta);
            }

            mysqli_query($db, "DELETE FROM producto_imagenes WHERE id = $img_id");
        }
    }

    header("Location: actualizar.php?id=$id");
    exit;
}

// Obtener producto
$query = "SELECT * FROM productos WHERE id = {$id}";
$resultado = mysqli_query($db, $query);
$producto = mysqli_fetch_assoc($resultado);

if(!$producto) {
    header('Location: /admin/');
    exit;
}

$errores = [];

// Categorías
$categorias = mysqli_query($db, "SELECT * FROM categorias ORDER BY nombre ASC");

// 🔥 IMÁGENES ACTUALES
$queryImgs = "SELECT * FROM producto_imagenes WHERE producto_id = $id";
$resultImgs = mysqli_query($db, $queryImgs);

// Valores
$nombre = $producto['nombre'];

// 🔥 Si no hay precio (NULL o 0), se muestra el campo vacío, no "0.00"
$precio = (empty($producto['precio']) || $producto['precio'] <= 0) ? '' : $producto['precio'];
$precio_original = (empty($producto['precio_original']) || $producto['precio_original'] <= 0) ? '' : $producto['precio_original'];

$descripcion = $producto['descripcion'];
$categoria = $producto['categoria'];
$categoria_id = $producto['categoria_id'];
$nueva_categoria = '';
$stock = $producto['stock'];
$caracteristicas = $producto['caracteristicas'];

if($_SERVER['REQUEST_METHOD'] === 'POST') {

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

    // 🔥 El precio es OPCIONAL. Si se deja en blanco, no hay error.
    // Si se pone, debe ser numérico y mayor a 0.
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

    // =====================================================
    // 🔥 CATEGORÍA: puede venir seleccionada, o puede pedirse
    // crear una categoría nueva en el momento ("nueva").
    // =====================================================
    if($categoria_id === 'nueva') {

        if($nueva_categoria === '') {
            $errores[] = "Escribe el nombre de la nueva categoría";
        } else {

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
    $precioSql = ($precio === '') ? 'NULL' : "'$precio'";
    $precioOriginalSql = ($precio_original === '') ? 'NULL' : "'$precio_original'";

    if(empty($errores)) {

        $carpetaImagenes = '../../imagenes/';
        $nombreImagen = $producto['imagen'];

        // 🔥 Imagen principal
        if($imagenPrincipal['name']) {

            if(file_exists($carpetaImagenes . $producto['imagen'])) {
                unlink($carpetaImagenes . $producto['imagen']);
            }

            $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

            move_uploaded_file(
                $imagenPrincipal['tmp_name'],
                $carpetaImagenes . $nombreImagen
            );
        }

        // 🔥 UPDATE PRODUCTO
        $query = "UPDATE productos SET 
            nombre = '$nombre',
            precio = $precioSql,
            precio_original = $precioOriginalSql,
            imagen = '$nombreImagen',
            descripcion = '$descripcion',
            categoria = '$categoria',
            categoria_id = '$categoria_id',
            stock = '$stock',
            caracteristicas = '$caracteristicas'
            WHERE id = {$id}
        ";

        $resultado = mysqli_query($db, $query);

        // 🔥 SUBIR GALERÍA (MÚLTIPLE)
        if(!empty($imagenes['name'][0])) {

            for($i = 0; $i < count($imagenes['name']); $i++) {

                if($imagenes['name'][$i]) {

                    $nombreImg = md5(uniqid(rand(), true)) . ".jpg";

                    move_uploaded_file(
                        $imagenes['tmp_name'][$i],
                        $carpetaImagenes . $nombreImg
                    );

                    $queryImg = "INSERT INTO producto_imagenes (producto_id, imagen)
                                 VALUES ($id, '$nombreImg')";

                    mysqli_query($db, $queryImg);
                }
            }
        }

        if($resultado) {
            header('Location: /admin/');
            exit;
        } else {
            $errores[] = "Ocurrió un error al actualizar el producto. Intenta nuevamente.";
        }
    }
}

incluirTemplate('header');
?>

<main class="contenedor admin-form">

    <h1>Actualizar Producto</h1>

    <?php foreach($errores as $error): ?>
        <p class="alerta-error"><?php echo $error; ?></p>
    <?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data" class="form-admin">

        <fieldset>
            <legend>Información General</legend>

            <div class="campo">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?php echo $nombre; ?>">
            </div>

            <div class="campo-grid">

                <div class="campo">
                    <label>Precio Original (opcional)</label>
                    <input type="number" step="0.01" name="precio_original" value="<?php echo $precio_original; ?>">
                </div>

                <div class="campo">
                    <label>Precio Oferta (opcional)</label>
                    <input type="number" step="0.01" name="precio" value="<?php echo $precio; ?>">
                </div>

            </div>

            <!-- IMAGEN PRINCIPAL -->
            <div class="campo">
                <label>Imagen Actual</label>
                <img class="img-preview" src="/imagenes/<?php echo $producto['imagen']; ?>">
            </div>

            <div class="campo">
                <label>Nueva Imagen Principal</label>
                <input type="file" name="imagen_principal" accept="image/jpeg, image/png">
            </div>

            <!-- GALERÍA -->
            <div class="campo">
                <label>Agregar imágenes a la galería</label>
                <input type="file" name="imagenes[]" id="inputGaleria" multiple accept="image/jpeg, image/png">

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

            <!-- 🔥 CAMPO PARA NUEVA CATEGORÍA -->
            <div class="campo" id="campoNuevaCategoria" style="<?php echo ($categoria_id === 'nueva') ? '' : 'display:none;'; ?>">
                <label>Nombre de la nueva categoría</label>
                <input type="text" name="nueva_categoria" id="inputNuevaCategoria" value="<?php echo $nueva_categoria; ?>" placeholder="Ej: Suplementos y Colágeno">
            </div>

            <input type="hidden" name="categoria" value="<?php echo $categoria; ?>">

        </fieldset>

        <input type="submit" value="Actualizar Producto" class="boton boton-full">

    </form>

    <!-- 🔥 GALERÍA ACTUAL -->
    <div class="galeria-admin">
        <h3>Imágenes actuales</h3>

        <div class="miniaturas">
            <?php while($img = mysqli_fetch_assoc($resultImgs)): ?>
                <div class="img-item">
                    <img src="/imagenes/<?php echo $img['imagen']; ?>">

                    <a href="?id=<?php echo $id; ?>&eliminar_img=<?php echo $img['id']; ?>" 
                       class="btn-eliminar"
                       onclick="return confirm('¿Eliminar esta imagen?')">
                        ✖
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

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
