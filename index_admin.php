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

// Lógica de Búsqueda
$resultados_busqueda = null;
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

if (!empty($busqueda)) {
    $termino = "%" . $busqueda . "%";

    // Unimos Pagos + Propietario + Residencias
    // Usamos LEFT JOIN en residencias por si el pago es de un propietario sin casa asignada aún
    $sql_search = "SELECT 
                        p.nombre, p.apellido, p.rif, 
                        pa.referencia, pa.monto, pa.fecha,
                        r.nro AS num_residencia
                   FROM pagos pa
                   INNER JOIN propietario p ON pa.id_propietario = p.id 
                   LEFT JOIN residencias r ON p.id = r.id_propietario
                   WHERE p.nombre LIKE ? 
                   OR p.apellido LIKE ? 
                   OR p.rif LIKE ? 
                   OR pa.referencia LIKE ?
                   OR r.nro LIKE ?
                   ORDER BY pa.fecha DESC";

    $stmt = $conexion->prepare($sql_search);
    // 5 's' porque son 5 campos de búsqueda (nombre, apellido, rif, referencia, casa)
    $stmt->bind_param("sssss", $termino, $termino, $termino, $termino, $termino);
    $stmt->execute();
    $resultados_busqueda = $stmt->get_result();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/dashboard.css">
    <style>
        /* Ocultar el encabezado de datos en la pantalla normal */
        .solo-impresion {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .panel_resultados,
            .panel_resultados * {
                visibility: visible;
            }

            .panel_resultados {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            /* Mostrar el encabezado solo al imprimir */
            .solo-impresion {
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                border-bottom: 2px solid #2c3e50;
                padding-bottom: 20px;
                margin-bottom: 20px;
            }

            button,
            .btn_imprimir,
            a {
                display: none !important;
            }

            table {
                box-shadow: none !important;
                border: 1px solid #ccc !important;
            }
        }
    </style>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=add" />
    <title>Panel Administrador</title>
    <link rel="icon" href="/img/ico_condo.ico">
</head>

<body>

    <!-- Aquí va el código de la página principal del panel de administrador -->
    <?php include 'navbar.php'; ?>

    <!-- Pantalla Principal -->
    <div class="principal">

        <!-- Encabezado Principal -->
        <div class="encabezado_principal">
            <div class="caja_titulo">
                <?php
                // Obtener el id_admin de la sesión
                $id_admin = $_SESSION['id_admin'];

                // Consulta usando el id_admin de la sesión
                $sql = "SELECT nombre_completo_admin, rif_admin, nombre_condominio FROM administrador WHERE id_admin = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("i", $id_admin);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    echo "<p><strong>" . htmlspecialchars($fila['nombre_condominio']) . "</strong></p>";
                    echo htmlspecialchars($fila['nombre_completo_admin']);
                    echo "<p><strong>" . htmlspecialchars($fila['rif_admin']) . "</strong></p>";
                } else {
                    echo "<p>No se encontraron datos para el administrador.</p>";
                }

                ?>
            </div>

        </div>

        <!-- BARRA DE BUSQUEDA Y TITULO -->

        <h1 class="titulo">Estadísticas</h1>

        <div class="busqueda">
            <form action="index_admin.php" method="GET" style="display: flex; width: 100%; align-items: center; gap: 10px;">
                <input
                    class="cuadro_busqueda"
                    type="text"
                    name="busqueda"
                    placeholder="Buscar: Ref, Nombre, RIF o Apto..."
                    value="<?php echo htmlspecialchars($busqueda); ?>">

                <button type="submit" class="filtros" style="border: none; cursor:pointer; background: transparent; display:flex; align-items:center;">
                    <img src="./img/icon/filtros.png" alt="Buscar" class="logo_filtros">
                    <p class="filtro">Buscar</p>
                </button>

                <?php if (!empty($busqueda)): ?>
                    <a href="index_admin.php" style="text-decoration: none; color: #000000; margin-left: 10px; font-size: 13px; display: flex; align-items: center; gap: 4px;">
                        <img src="./img/clean.png" alt="Limpiar" style="width: 18px; height: 18px; vertical-align: middle;">
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- MPANEL DE DE ADMINISTRACION  -->
        <div class="panel">

            <div class="first_row">
                <!-- PANEL FACTURAS PENDIENTES -->
                <a href="./facturas_pendientes.php">
                    <div class="marcos_panel">
                        <div class="para_pendientes">
                            <?php
                            // Consulta para contar los registros con status "Pendiente" y fecha de vencimiento menor a la fecha actual
                            $sql_morosos = "SELECT COUNT(*) AS total_pendientes 
                                        FROM facturas 
                                        WHERE status = 'Pendiente' 
                                        AND fecha_vencimiento >= (SELECT MAX(fecha_emision) FROM facturas)";
                            $resultado_morosos = $conexion->query($sql_morosos);

                            // Verificamos si hay resultados
                            if ($resultado_morosos->num_rows > 0) {
                                $fila_morosos = $resultado_morosos->fetch_assoc();
                                $total_pendientes = $fila_morosos['total_pendientes'];
                            } else {
                                $total_pendientes = 0;
                            }
                            ?>
                            <h1 class="numeros_morosos"><?php echo $total_pendientes; ?></h1>
                            <p>Facturas pendientes</p>
                        </div>
                    </div>
                </a>

                <!-- PANEL BUSQUEDA-->

                <?php include 'modelo/ultimos_pagos.php'; ?>
            </div>
            <div id="publicaciones">

                <?php if (!empty($busqueda)): ?>
                    <div class="panel_resultados" style="margin: 20px 0; padding: 0 5%;">

                        <?php
                        // Consultamos todos los datos del administrador para el reporte
                        $id_admin_reporte = $_SESSION['id_admin'];
                        $sql_admin = "SELECT usuario_admin, nombre_completo_admin, rif_admin, rol_admin, nombre_condominio, direccion_condominio 
                      FROM administrador WHERE id_admin = ?";
                        $stmt_admin = $conexion->prepare($sql_admin);
                        $stmt_admin->bind_param("i", $id_admin_reporte);
                        $stmt_admin->execute();
                        $admin_data = $stmt_admin->get_result()->fetch_assoc();
                        ?>
                        <!-- Encabezado para impresión-->
                        <div class="solo-impresion">
                            <div style="width: 70%;">
                                <h1 style="margin: 0; color: #2c3e50;"><?php echo htmlspecialchars($admin_data['nombre_condominio']); ?></h1>
                                <p style="margin: 5px 0;"><strong>RIF:</strong> <?php echo htmlspecialchars($admin_data['rif_admin']); ?></p>
                                <p style="margin: 2px 0;"><strong>Dirección:</strong> <?php echo htmlspecialchars($admin_data['direccion_condominio']); ?></p>
                                <hr style="margin: 10px 0; border: 0; border-top: 1px solid #eee;">
                                <p style="margin: 2px 0;"><strong>Nombre:</strong> <?php echo htmlspecialchars($admin_data['nombre_completo_admin']); ?>(<?php echo htmlspecialchars($admin_data['rol_admin']); ?>)</p>
                                <p style="margin: 2px 0;"><strong>Usuario:</strong> <?php echo htmlspecialchars($admin_data['usuario_admin']); ?></p>
                                <p style="margin: 2px 0;"><strong>Fecha de Reporte:</strong> <?php
                                    date_default_timezone_set('America/Caracas');
                                    echo date('d/m/Y h:i A');
                                ?>
                            </div>
                            <div style="width: 25%; text-align: right;">
                                <img src="./img/icono_condo.jpg" alt="Logo" style="width: 120px; height: auto; border-radius: 8px;">
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h2 style="color: #333; margin: 0;">Reporte de Búsqueda: "<?php echo htmlspecialchars($busqueda); ?>"</h2>

                            <?php if ($resultados_busqueda && $resultados_busqueda->num_rows > 0): ?>
                                <button onclick="window.print()" class="btn_imprimir" style="background-color: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: bold;">
                                    <span class="material-symbols-outlined"></span> Guardar PDF / Imprimir
                                </button>
                            <?php endif; ?>
                        </div>

                        <?php if ($resultados_busqueda && $resultados_busqueda->num_rows > 0): ?>
                            <table style="width:100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;">
                                <thead style="background: #2c3e50; color: white;">
                                    <tr>
                                        <th style="padding: 12px; text-align: left;">Propietario</th>
                                        <th style="padding: 12px;">RIF / CI</th>
                                        <th style="padding: 12px;">Residencia</th>
                                        <th style="padding: 12px;">Referencia</th>
                                        <th style="padding: 12px;">Monto</th>
                                        <th style="padding: 12px;">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $resultados_busqueda->fetch_assoc()): ?>
                                        <tr style="border-bottom: 1px solid #eee;">
                                            <td style="padding: 10px;">
                                                <?php echo htmlspecialchars($row['nombre'] . " " . $row['apellido']); ?>
                                            </td>
                                            <td style="text-align: center;"><?php echo htmlspecialchars($row['rif']); ?></td>
                                            <td style="text-align: center; font-weight: bold; color: #2980b9;">
                                                <?php echo !empty($row['num_residencia']) ? htmlspecialchars($row['num_residencia']) : 'N/A'; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <span style="background: #e0f7fa; padding: 3px 8px; border-radius: 4px; font-family: monospace;">
                                                    <?php echo htmlspecialchars($row['referencia']); ?>
                                                </span>
                                            </td>
                                            <td style="text-align: center; font-weight: bold; color: #27ae60;">
                                                <?php echo number_format($row['monto'], 2, ',', '.'); ?> Bs
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo isset($row['fecha']) ? date("d/m/Y", strtotime($row['fecha'])) : '-'; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; border: 1px solid #ffeeba;">
                                No se encontraron pagos, propietarios o residencias con esa información.
                            </div>
                        <?php endif; ?>
                        <hr style="margin-top: 30px; border: 0; border-top: 2px dashed #ccc;">
                    </div>
                <?php endif; ?>

            </div>
            <?php
            // Consulta para obtener el último registro de la tabla 'factor'
            $sql = "SELECT factor, fecha FROM factor ORDER BY id DESC LIMIT 1";
            $resultado = $conexion->query($sql);

            if ($resultado && $resultado->num_rows > 0) {
                $fila = $resultado->fetch_assoc();
                $ultimo_factor = $fila['factor'];
                $ultima_fecha = $fila['fecha'];

                echo "<p>Factor: $ultimo_factor Bs</p>";
                echo "<p>Fecha: $ultima_fecha</p>";
            } else {
                echo "<p>No hay registros en la tabla 'factor'.</p>";
            }
            ?>
        </div>






    </div>
    </section>

    </div>
    </div>
</body>

</html>