<?php
include './modelo/conexion.php';

session_start();
if (empty($_SESSION['id_admin'])) {
    echo " <script languaje='JavaScript'>
    alert('Estas intentando entrar al Sistema sin haberte registrado o iniciado sesión');
    location.assign('login.php');
    </script>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos del Condominio</title>
       <link rel="icon" href="/img/ico_condo.ico">
    <link rel="stylesheet" href="./css/estilos.css"> 
</head>
<body>
    <h1>Formulario de Datos del Condominio</h1>
    <form action="./modelo/guardar_datos_condominio.php" method="POST">
        <label for="rif">RIF:</label>
        <input type="text" id="rif" name="rif" placeholder="Ingrese el RIF" required>
        <br><br>

        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" placeholder="Ingrese el nombre del condominio" required>
        <br><br>

        <label for="direccion">Dirección:</label>
        <textarea id="direccion" name="direccion" placeholder="Ingrese la dirección del condominio" rows="4" required></textarea>
        <br><br>

        <button type="submit">Guardar</button>
    </form>
</body>
</html>