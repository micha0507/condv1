<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/navbar.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=add" />

    <title>Document</title>
</head>

<body>

    <!-- barra de navegación -->
    <!-- boton y logo -->
    <nav class="navegacion">
        <div class="div_logo">
            <a href="./index_admin.php">
                <img src="./img/icono_condo.jpg" alt="logo tecnostar" class="logo">
            </a>
        </div>
        <!-- BOTON -->
        <a href="carga_pago.php" class="carga">

            <span class="material-symbols-outlined">
                add
            </span>
            <p>Cargar pago</p>


        </a>
        <!-- botones menu nav -->
        <div class="menu_nav">
            <ul class="botones_menu">
                <li class="boton_menu"><img src="./img/icon/logo_estadistica.png"><a href="index_admin.php">Estadísticas<a></li>
                <li class="boton_menu"><img src="./img/icon/factor.png"><a href="./pagos.php">Pagos</a></li>
                <li class="boton_menu"><img src="./img/icon/masivo_icon.png"><a href="./carga_masiva.php">Carga masiva</a></li>
                <li class="boton_menu"><img src="./img/icon/eventuales_icon.png"><a href="./gastos_eventuales.php">Gastos eventuales</a></li>
                <li class="boton_menu"><img src="./img/icon/miembros_icon.png"><a href="./anadir_miembro.php">Añadir miembro</a></li>
                <li class="boton_menu"><img src="./img/icon/factor.png"><a href="./factorcambiario.php">Factor cambiario</a></li>
                <li class="boton_menu"><img src="./img/icon/factor.png"><a href="./fondos.php">Fondos</a></li>
                <li class="boton_menu">
                    <img src="./img/avatar.svg" alt="icono cerrar sesión">
                    <a href="/controlador/controlador_cerrar_sesion.php?rol=Administrador">Cerrar sesión</a>
                </li>
            <div class="periodo-actual" style="margin-top: 15px; padding: 10px; border-top: 1px solid #ddd; font-size: 0.9em; color: #666;">
                <?php
                $meses = [
                    1 => 'Enero',
                    2 => 'Febrero',
                    3 => 'Marzo',
                    4 => 'Abril',
                    5 => 'Mayo',
                    6 => 'Junio',
                    7 => 'Julio',
                    8 => 'Agosto',
                    9 => 'Septiembre',
                    10 => 'Octubre',
                    11 => 'Noviembre',
                    12 => 'Diciembre'
                ];
                $mes_actual = $meses[(int)date('m')];
                $anio_actual = date('Y');
                echo "<strong>Periodo:</strong> $mes_actual $anio_actual";
                ?>
            </div>
            </ul>

        </div>
    </nav>
</body>

</html>