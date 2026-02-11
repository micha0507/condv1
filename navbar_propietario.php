<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/navbar.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<body>
    <nav class="navegacion">
        <div class="div_logo">
            <a href="../propietario.php">
                <img src="./img/icono_condo.jpg" alt="Logo" class="logo">
            </a>
        </div>
        <!-- BOTON -->
        <a href="../modelo/pagos_propietario.php" class="carga">

            <span class="material-symbols-outlined">
                add
            </span>
            <p>Cargar pago</p>
        </a>
        <div class="menu_nav">
            <ul class="botones_menu">
                <li class="boton_menu">
                    <img src="./img/icon/logo_estadistica.png">
                    <a href="../propietario.php">Mi Cuenta</a>
                </li>
                <li class="boton_menu">
                    <img src="./img/icon/factor.png">
                    <a href="/modelo/pagos_propietario.php">Ver Pagos</a>
                </li>
                <li class="boton_menu">
                    <img src="./img/avatar.svg" alt="Cerrar sesión">
<a href="controlador/controlador_cerrar_sesion.php?rol=Propietario">Cerrar sesión</a>                </li>
            </ul>

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
        </div>
    </nav>
</body>

</html>