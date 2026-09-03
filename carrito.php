<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'includes/app.php';

$carrito = $_SESSION['carrito'] ?? [];
$total = 0;

incluirTemplate('header');
?>

<main class="contenedor">
    <h1>Carrito de Compras</h1>

    <?php if(empty($carrito)): ?>
        <p>El carrito está vacío</p>
    <?php else: ?>

        <table class="tabla-carrito">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($carrito as $id => $producto): 
                    $tienePrecio = !empty($producto['precio']) && $producto['precio'] > 0;
                    $subtotal = $tienePrecio ? ($producto['precio'] * $producto['cantidad']) : 0;
                    $total += $subtotal;
                ?>
                <tr>

                    <!-- 🔥 PRODUCTO CON IMAGEN -->
                    <td class="producto-info">
                        <img src="/imagenes/<?php echo $producto['imagen'] ?? 'no-image.jpg'; ?>" alt="">
                        <span><?php echo $producto['nombre']; ?></span>
                    </td>

                    <td>
                        <?php if($tienePrecio): ?>
                            S/.<?php echo number_format($producto['precio'], 2, '.', "'"); ?>
                        <?php else: ?>
                            <span class="precio-consultar">Consultar precio</span>
                        <?php endif; ?>
                    </td>

                    <!-- 🔥 CANTIDAD PRO -->
                    <td class="cantidad">
                        <form method="POST" action="actualizar-carrito.php">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">

                            <button name="accion" value="restar">-</button>

                            <span><?php echo $producto['cantidad']; ?></span>

                            <button name="accion" value="sumar">+</button>
                        </form>
                    </td>

                    <td>
                        <?php if($tienePrecio): ?>
                            S/.<?php echo number_format($subtotal, 2, '.', "'"); ?>
                        <?php else: ?>
                            <span class="precio-consultar">Consultar precio</span>
                        <?php endif; ?>
                    </td>

                    <!-- 🔥 ELIMINAR -->
                    <td>
                        <form method="POST" action="eliminar-carrito.php">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            <button class="eliminar">X</button>
                        </form>
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
            // 🔥 ¿Hay algún producto sin precio en el carrito?
            $hayProductosSinPrecio = false;
            foreach($carrito as $producto) {
                if(empty($producto['precio']) || $producto['precio'] <= 0) {
                    $hayProductosSinPrecio = true;
                    break;
                }
            }
        ?>

        <!-- 🔥 TOTAL -->
        <div class="total-carrito">
            <h2>Total: S/.<?php echo number_format($total, 2, '.', "'"); ?></h2>

            <?php if($hayProductosSinPrecio): ?>
                <p class="nota-sin-precio">* Hay productos a consultar, no incluidos en este total.</p>
            <?php endif; ?>
        </div>

        <!-- 🔥 WHATSAPP -->
        <?php
        $mensaje = "Hola, quiero comprar:%0A%0A";

        foreach($carrito as $producto) {

            $tienePrecioMsg = !empty($producto['precio']) && $producto['precio'] > 0;

            $mensaje .= "- " . urlencode($producto['nombre']) . 
                        " x" . $producto['cantidad'] . " unidades";

            if($tienePrecioMsg) {
                $mensaje .= " (S/." . number_format($producto['precio'], 2, '.', "'") . ")%0A";
            } else {
                $mensaje .= " (a consultar)%0A";
            }
        }

        $mensaje .= "%0ATotal: S/." . number_format($total, 2, '.', "'");

        if($hayProductosSinPrecio) {
            $mensaje .= " (+ precio a consultar)";
        }

        $telefono = SITIO_WHATSAPP;
        $url = "https://wa.me/" . $telefono . "?text=" . $mensaje;
        ?>

        <a href="<?php echo $url; ?>" target="_blank" class="boton boton-whatsapp" onclick="vaciarCarrito()">
            Comprar por WhatsApp
        </a>

        <script>
            function vaciarCarrito() {
                fetch('/vaciar-carrito.php');
            }
        </script>

    <?php endif; ?>

</main>

<?php
incluirTemplate('footer');