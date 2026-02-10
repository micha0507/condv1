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
                    <a href="/controlador/controlador_cerrar_sesion.php">Cerrar sesión</a>
                </li>
                <div class="periodo-actual" style="padding: 10px; text-align: center; background: #f5f5f5;"><br>
                    <?php
                    // Ejemplo: mostrar el periodo actual (mes y año) usando date() en lugar de strftime
                    setlocale(LC_TIME, 'es_ES.UTF-8');
                    $mes = ucfirst(strftime('%B')); // Para compatibilidad, pero strftime está obsoleto
                    if (function_exists('date')) {
                        // Usar date y traducción manual
                        $meses = [
                            'January' => 'Enero',
                            'February' => 'Febrero',
                            'March' => 'Marzo',
                            'April' => 'Abril',
                            'May' => 'Mayo',
                            'June' => 'Junio',
                            'July' => 'Julio',
                            'August' => 'Agosto',
                            'September' => 'Septiembre',
                            'October' => 'Octubre',
                            'November' => 'Noviembre',
                            'December' => 'Diciembre'
                        ];
                        $mes_en = date('F');
                        $mes = $meses[$mes_en] ?? $mes_en;
                    }
                    $anio = date('Y');
                    echo "Periodo actual: $mes $anio";
                    ?>
                </div>
            </ul>

        </div>
    </nav>
</body>

</html>