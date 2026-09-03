<?php
// INICIAR SESIÓN DE FORMA SEGURA
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CONTADOR DEL CARRITO
$totalItems = 0;

if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $producto) {
        $totalItems += isset($producto['cantidad']) ? (int)$producto['cantidad'] : 1;
    }
}

// =========================================================
// 🔥 MENÚ DE CATEGORÍAS DINÁMICO
// Se arma con 2 consultas livianas (sin SELECT *) para que
// el menú se actualice solo cuando se crean categorías/productos
// desde el panel de administración.
// =========================================================
$categoriasMenu = [];

if (isset($db)) {

    $resCategoriasMenu = mysqli_query($db, "SELECT id, nombre FROM categorias ORDER BY nombre ASC");

    if ($resCategoriasMenu) {
        while ($cat = mysqli_fetch_assoc($resCategoriasMenu)) {
            $categoriasMenu[$cat['id']] = [
                'nombre'    => $cat['nombre'],
                'productos' => [],
            ];
        }
    }

    if (!empty($categoriasMenu)) {

        $resProductosMenu = mysqli_query(
            $db,
            "SELECT id, nombre, categoria_id FROM productos ORDER BY creado DESC"
        );

        if ($resProductosMenu) {
            while ($p = mysqli_fetch_assoc($resProductosMenu)) {
                if (
                    isset($categoriasMenu[$p['categoria_id']]) &&
                    count($categoriasMenu[$p['categoria_id']]['productos']) < 6
                ) {
                    $categoriasMenu[$p['categoria_id']]['productos'][] = $p;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- 🔥 SEO BÁSICO -->
    <title>Casa Naturista La Huanuqueña | Productos Naturales en Perú</title>
    <meta name="description" content="<?php echo SITIO_DESCRIPCION; ?>">
    <meta name="keywords" content="productos naturistas, polen, propóleo, huanarpo macho, moringa, maca, colágeno hidrolizado, suplementos naturales, Huánuco, Perú">
    <meta name="author" content="Casa Naturista La Huanuqueña">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo SITIO_URL; ?>/">

    <!-- 🔥 OPEN GRAPH (para que se vea bien al compartir en WhatsApp, Facebook, etc) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Casa Naturista La Huanuqueña | Productos Naturales en Perú">
    <meta property="og:description" content="<?php echo SITIO_DESCRIPCION; ?>">
    <meta property="og:image" content="<?php echo SITIO_URL; ?>/build/img/og/og-cover.jpg">
    <meta property="og:url" content="<?php echo SITIO_URL; ?>/">
    <meta property="og:locale" content="es_PE">
    <meta property="og:site_name" content="Casa Naturista La Huanuqueña">

    <!-- 🔥 TWITTER CARD -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Casa Naturista La Huanuqueña | Productos Naturales en Perú">
    <meta name="twitter:description" content="<?php echo SITIO_DESCRIPCION; ?>">
    <meta name="twitter:image" content="<?php echo SITIO_URL; ?>/build/img/og/og-cover.jpg">

    <!-- 🔥 FAVICON -->
    <link rel="icon" type="image/png" sizes="96x96" href="/build/img/favicon/favicon-96x96.png">
    <link rel="icon" type="image/svg+xml" href="/build/img/favicon/favicon.svg">
    <link rel="shortcut icon" href="/build/img/favicon/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/build/img/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/build/img/favicon/site.webmanifest">
    <meta name="theme-color" content="#1f7a3d">

    <!-- 🔥 PERFORMANCE: preconnect a dominios externos que uses -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- 🔥 PERFORMANCE: precargar el CSS crítico -->
    <link rel="preload" href="/build/css/app.css" as="style">
    <link rel="stylesheet" href="/build/css/app.css">

    <!-- 🔥 Hero: precarga condicional según el ancho de pantalla -->
    <link rel="preload" as="image" href="/build/img/hero/hero-movil.jpg" media="(max-width: 768px)">
    <link rel="preload" as="image" href="/build/img/hero/hero.jpg" media="(min-width: 630px)">

    <!-- 🔥 Dato usado por el JS del botón flotante de WhatsApp (evita repetir el número en el JS) -->
    <script>window.WHATSAPP_NUMERO = "<?php echo SITIO_WHATSAPP; ?>";</script>

</head>

<body>

<header class="header" id="siteHeader">

    <!-- HEADER PRINCIPAL -->
    <div class="header-top contenedor">

        <!-- LOGO -->
        <div class="logo">
            <a class="img-logo" href="/">
                <img src="/build/img/logos/logo.svg" alt="Logo Casa Naturista La Huanuqueña">
            </a>
        </div>

       <!-- 🛒 CARRITO -->
        <div class="acciones">
            <a href="/carrito.php" id="btnCarrito" class="carrito">
                🛒 <span id="contadorCarrito">(<?php echo $totalItems; ?>)</span>
            </a>
        </div>

        <!-- BUSCADOR -->
        <div class="buscador">
            <input 
                type="text" 
                id="buscadorInput"
                placeholder="Busca productos naturales..."
                autocomplete="off"
            >
            <div id="resultadosBusqueda" class="resultados-busqueda"></div>
        </div>

        
 <!-- CATEGORÍAS (DESKTOP) -->

        <!-- HAMBURGUESA MOBILE -->
         <div>
            <div class="menu-toggle" id="menu-toggle">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 6H25" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    <path d="M1 12H25" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                    <path d="M1 18H25" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

         </div>
        

    </div>

    <!-- MEGA MENÚ (DINÁMICO: se arma solo con las categorías/productos creados desde el admin) -->
   <div class="menu-categorias">

    <ul class="menu-horizontal">

        <?php foreach ($categoriasMenu as $catId => $cat): ?>
            <li>
                <a href="/categoria.php?id=<?php echo $catId; ?>" class="menu-padre">
                    <?php echo htmlspecialchars($cat['nombre']); ?>
                </a>

                <?php if (!empty($cat['productos'])): ?>
                    <ul class="submenu">
                        <?php foreach ($cat['productos'] as $p): ?>
                            <li>
                                <a href="/producto.php?id=<?php echo $p['id']; ?>">
                                    <?php echo htmlspecialchars($p['nombre']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>

    </ul>

</div>

    <!-- MENÚ PRINCIPAL -->
    <div class="menu-bar">
        <nav id="menu" class="contenedor">
            <!-- MOBILE CATEGORÍAS (DINÁMICO) -->
            <div class="categorias-mobile">

                <?php foreach ($categoriasMenu as $catId => $cat): ?>

                    <a href="/categoria.php?id=<?php echo $catId; ?>" class="grupo-cat">
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </a>

                    <?php foreach ($cat['productos'] as $p): ?>
                        <a href="/producto.php?id=<?php echo $p['id']; ?>">
                            <?php echo htmlspecialchars($p['nombre']); ?>
                        </a>
                    <?php endforeach; ?>

                <?php endforeach; ?>

            </div>

            <?php if(isset($_SESSION['login']) && $_SESSION['login']): ?>
                <a href="/admin/">Admin</a>
                <a href="/logout.php">Cerrar Sesión</a>
            <?php endif; ?>

        </nav>
    </div>

</header>

<!-- 🔥 MINI CARRITO -->
<div id="miniCarrito" class="mini-carrito">

    <div class="mini-header">
        <h3>🛒 Tu carrito</h3>
        <span id="cerrarMini">✖</span>
    </div>

    <div id="miniContenido" class="mini-contenido">
        <p>Tu carrito está vacío</p>
    </div>

    <a href="/carrito.php" class="boton">
        Ver carrito
    </a>

</div>

<!-- 🔥 MODAL -->
<div id="modalCarrito" class="modal-carrito">
    <div class="modal-contenido">

        <h3>Producto agregado 🛒</h3>
        <p id="productoAgregado"></p>

        <div class="modal-botones">
            <button id="seguirComprando" class="boton-secundario">
                Seguir comprando
            </button>

            <a href="/carrito.php" class="boton">
                Ir al carrito
            </a>
        </div>

    </div>
</div>

<!-- OVERLAY -->
<div id="overlayCarrito" class="overlay-carrito"></div>
