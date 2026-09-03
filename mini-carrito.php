<?php
session_start();

$carrito = $_SESSION['carrito'] ?? [];
$total = 0;
?>

<?php if(empty($carrito)): ?>
<p>Carrito vacío</p>
<?php else: ?>

<?php foreach($carrito as $id=>$producto): 
    $subtotal = $producto['precio']*$producto['cantidad'];
    $total += $subtotal;
?>

<div class="mini-item">
    <img src="/imagenes/<?php echo $producto['imagen']; ?>">

    <div>
        <p><?php echo $producto['nombre']; ?></p>
        <p>
            $<?php echo $producto['precio']; ?> x 
            <span class="mini-item-cantidad"><?php echo $producto['cantidad']; ?></span>
        </p>
    </div>

    <button class="eliminar-item" data-id="<?php echo $id; ?>">✖</button>
</div>

<?php endforeach; ?>

<div class="mini-total">
    Total: $<?php echo $total; ?>
</div>

<?php endif; ?>