<?php

session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'];
    $accion = $_POST['accion'];

    if(isset($_SESSION['carrito'][$id])) {

        if($accion === 'sumar') {
            $_SESSION['carrito'][$id]['cantidad']++;
        }

        if($accion === 'restar') {
            $_SESSION['carrito'][$id]['cantidad']--;

            // Si llega a 0 → eliminar
            if($_SESSION['carrito'][$id]['cantidad'] <= 0) {
                unset($_SESSION['carrito'][$id]);
            }
        }
    }

    header('Location: carrito.php');
}
