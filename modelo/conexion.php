<?php

// modelo/conexion.php
$conexion = new mysqli("localhost", "root", "", "condominio");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
$conexion->set_charset("utf8");
?>
