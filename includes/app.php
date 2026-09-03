<?php

require 'funciones.php';
require 'config/config.php';
require 'config/database.php';

$db = conectarDB();

function estaAutenticado() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(empty($_SESSION['login'])) {
        header('Location: /login.php');
        exit;
    }
}