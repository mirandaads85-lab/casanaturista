<?php

function incluirTemplate($nombre) {
    global $db;
    include __DIR__ . "/templates/${nombre}.php";
}