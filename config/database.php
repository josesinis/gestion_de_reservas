<?php

$host = "localhost";
$db   = "db_portal_escolar";
$user = "root";
$pass = "";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión.");
}

$conexion->set_charset("utf8mb4");
