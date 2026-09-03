<?php

require 'funciones.php';
require 'config/config.php';
require 'config/database.php';

$db = conectarDB();

function estaAutenticado() {
    session_start();

    if(!$_SESSION['login']) {
        header('Location: /login.php');
    }
}