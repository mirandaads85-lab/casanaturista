<?php

function conectarDB() : mysqli {

    // 🔥 Usa variables de entorno del hosting si existen (Render, etc.)
    //    y si no, cae en los valores locales de XAMPP como respaldo.
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';
    $name = getenv('DB_NAME') ?: 'casanaturista';
    $port = getenv('DB_PORT') ?: 3307;

    mysqli_report(MYSQLI_REPORT_OFF);
    $db = mysqli_connect($host, $user, $pass, $name, (int) $port);

    if(!$db) {
        echo "Error no se pudo conectar a la base de datos: " . mysqli_connect_error();
        exit;
    }

    // 🔥 Necesario para que tildes y "ñ" se muestren bien (ej: Huanuqueña, Colágeno)
    mysqli_set_charset($db, 'utf8mb4');

    return $db;
}