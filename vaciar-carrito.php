<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vaciar carrito
$_SESSION['carrito'] = [];

// Redirigir al inicio
header('Location: /casanaturista');