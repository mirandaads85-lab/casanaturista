<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'includes/app.php';

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = intval($_POST['id']);

    $query = "SELECT * FROM productos WHERE id = $id";
    $resultado = mysqli_query($db, $query);
    $producto = mysqli_fetch_assoc($resultado);

    if(!$producto){
        echo json_encode(['ok'=>false]);
        exit;
    }

    if(!isset($_SESSION['carrito'])){
        $_SESSION['carrito'] = [];
    }

    // 🔥 Determinar el precio real a usar: oferta si es válida, si no el original, si no hay ninguno, null
    $tieneOferta = !empty($producto['precio'])
        && $producto['precio'] > 0
        && !empty($producto['precio_original'])
        && $producto['precio_original'] > 0
        && $producto['precio'] < $producto['precio_original'];

    $tieneAlgunPrecio = !empty($producto['precio_original']) && $producto['precio_original'] > 0;

    if($tieneOferta) {
        $precioFinal = $producto['precio'];
    } elseif($tieneAlgunPrecio) {
        $precioFinal = $producto['precio_original'];
    } else {
        $precioFinal = null;
    }

    if(isset($_SESSION['carrito'][$id])){
        $_SESSION['carrito'][$id]['cantidad']++;
    } else {
        $_SESSION['carrito'][$id] = [
            'nombre'=>$producto['nombre'],
            'precio'=>$precioFinal,
            'imagen'=>$producto['imagen'],
            'cantidad'=>1
        ];
    }

    echo json_encode([
        'ok'=>true,
        'nombre'=>$producto['nombre']
    ]);
}