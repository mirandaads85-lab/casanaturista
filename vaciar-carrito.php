<?php

session_start();

// Vaciar carrito
$_SESSION['carrito'] = [];

// Redirigir al inicio
header('Location: /casanaturista');