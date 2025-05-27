<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./css/navbar.css">
    <link rel="stylesheet" href="./css/fondos.css">
    <title>Fondos</title>
</head>
<body>
    <?php include 'navbar.php'; ?> <!-- Incluir la barra de navegación -->
 
    <div class="contenido">
        <h1>Resumen de Fondos</h1>
        <div class="resumen-fondos">
            <?php include './modelo/calcula_fondos.php'; ?> <!-- Incluir el cálculo de fondos -->
        </div>

     

    <!-- Incluir el modal -->
    <?php include './modelo/modal_gastos_mes.php'; ?> <!-- Modal de gastos del mes -->
    <?php include './modelo/modal_pagos_mes.php'; ?> <!-- Modal de pagos del mes -->

</body>
</html>
