<?php

require 'includes/app.php';

$busqueda = $_GET['q'] ?? '';
$busqueda = trim($busqueda);

if($busqueda === '') {
    echo json_encode([]);
    exit;
}

$busqueda = mysqli_real_escape_string($db, $busqueda);

$query = "SELECT id, nombre, precio, imagen 
          FROM productos 
          WHERE nombre LIKE '%${busqueda}%'
          LIMIT 5";

$resultado = mysqli_query($db, $query);

$productos = [];

while($row = mysqli_fetch_assoc($resultado)) {
    $productos[] = $row;
}

echo json_encode($productos);