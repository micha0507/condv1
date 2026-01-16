<?php
include './modelo/conexion.php';


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/reportes.css">
    <title>Editor de publicaciones</title>
       <link rel="icon" href="/img/ico_condo.ico">
</head>
<body>
    <?php include 'navbar.php'; ?> <!-- Incluir la barra de navegación -->

    <div class="principal">
      
       <div class="cuadros">
            <div class="cuadro_balance">
                <h4 class="titulo_balance">Balance de pagos</h4>
                <div class="grafico_balance"></div>
                <div class="leyenda_balance"></div>
                <div class="leyenda_balance"></div>
                <h5>Fondos</h5>
                <h4 class="fondos_numeros">1123321 Bs</h4>
            </div>
            <div class="grafico">
                 <div class="titulo"><h2>Ingresos del perido actual</h2></div>
            </div>
       </div>
       <div class="marco_cuadros_report">
        <div class="lineas" id="L1">
            <a href="#" class="cuadros_reportes"><img src="./img/icon/ingresoDinero.png" alt="ingreso">Ingresos mensuales</a>
            <a href="#" class="cuadros_reportes"><img src="./img/icon/ingresoDinero.png" alt="ingreso">Ingresos anuales</a>
            <a href="#" class="cuadros_reportes"><img src="./img/icon/historial.png" alt="ingreso">Historico cierre mensual</a>
        </div>
           
        <div class="lineas" id="L2">
                 <a href="#" class="cuadros_reportes"><img src="./img/icon/perdida dinero (1).png" alt="ingreso">Egresos mensuales</a>
            <a href="#" class="cuadros_reportes"><img src="./img/icon/perdida dinero (1).png" alt="ingreso">Egresos anuales</a>
            <a href="#" class="cuadros_reportes"><img src="./img/icon/historial.png" alt="ingreso">Historico propietario</a>
        </div>
           
        <div class="lineas" id="L3">

       
            <a href="#" class="cuadros_reportes"><img src="./img/icon/historial.png" alt="ingreso">Historico Factor de cambio</a>
            <a href="#" class="cuadros_reportes"><img src="./img/icon/historial.png" alt="ingreso">Bitacora</a>
         </div>  
       </div>         
    </div>


</body>
</html>