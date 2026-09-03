<?php

session_start();

// Vaciar sesión
$_SESSION = [];

// Destruir sesión
session_destroy();

// Redirigir
header('Location: /casanaturista');