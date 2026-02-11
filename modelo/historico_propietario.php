<?php
include '../modelo/conexion.php';
session_start();

// 1. Validación de sesión de propietario
if (empty($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$id_propietario = $_SESSION['id'];

// Configuración de zona horaria (basado en pagos.php)
date_default_timezone_set('America/Caracas');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Histórico de Pagos</title>
    <link rel="icon" href="/img/ico_condo.ico">
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="stylesheet" href="./css/tabla_pagos.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    
    <?php include '../navbar_propietario.php'; ?>

    <div class="principal">
        <div class="encabezado_principal">
            <div class="caja_titulo">
                <h1>Historial de Pagos Realizados</h1>
                <p>Consulta el estado de todos tus reportes de pago y comprobantes.</p>
            </div>
        </div>

        <div class="busqueda" style="margin-bottom: 20px;">
            <form method="GET" action="" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center;">
                <div>
                    <label for="status">Estado:</label>
                    <select id="status" name="status" class="cuadro_busqueda" style="width: auto;">
                        <option value="">Todos</option>
                        <option value="pendiente" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="validado" <?php echo (isset($_GET['status']) && $_GET['status'] == 'validado') ? 'selected' : ''; ?>>Validado</option>
                    </select>
                </div>

                <div>
                    <label for="fecha">Fecha específica:</label>
                    <input type="date" id="fecha" name="fecha" class="cuadro_busqueda" style="width: auto;" 
                           value="<?php echo isset($_GET['fecha']) ? htmlspecialchars($_GET['fecha']) : ''; ?>">
                </div>

                <button type="submit" class="filtros" style="border: none; cursor:pointer; background: #2c3e50; color: white; padding: 10px 20px; border-radius: 5px;">
                    Filtrar Historial
                </button>

                <?php if (!empty($_GET)): ?>
                    <a href="pagos_propietario.php" style="text-decoration: none; font-size: 13px; color: #e74c3c;">Limpiar Filtros</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="tabla_contenedor">
            <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                <thead style="background: #2c3e50; color: white;">
                    <tr>
                        <th style="padding: 12px;">Fecha Pago</th>
                        <th style="padding: 12px;">Referencia</th>
                        <th style="padding: 12px;">Monto</th>
                        <th style="padding: 12px;">Estado</th>
                        <th style="padding: 12px;">Factura Ref.</th>
                        <th style="padding: 12px;">Registro en Sistema</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Construcción de consulta filtrada por seguridad para el propietario logueado
                    $where = ["id_propietario = $id_propietario"];

                    if (!empty($_GET['status'])) {
                        $status = $conexion->real_escape_string($_GET['status']);
                        $where[] = "status = '$status'";
                    }

                    if (!empty($_GET['fecha'])) {
                        $fecha = $conexion->real_escape_string($_GET['fecha']);
                        $where[] = "DATE(fecha) = '$fecha'";
                    }

                    $sql_where = "WHERE " . implode(" AND ", $where);

                    $sql = "SELECT * FROM pagos $sql_where ORDER BY fecha DESC";
                    $result = $conexion->query($sql);

                    if ($result && $result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            $status_class = ($row['status'] == 'validado') ? 'color: #27ae60; font-weight: bold;' : 'color: #f39c12;';
                    ?>
                        <tr style="border-bottom: 1px solid #eee; text-align: center;">
                            <td style="padding: 12px;"><?= date("d/m/Y", strtotime($row['fecha'])) ?></td>
                            <td style="font-family: monospace;"><?= htmlspecialchars($row['referencia']) ?></td>
                            <td style="font-weight: bold;"><?= number_format($row['monto'], 2, ',', '.') ?> Bs</td>
                            <td style="<?= $status_class ?>">
                                <?= ($row['status'] == 'validado') ? '✔ Validado' : '⏳ Pendiente' ?>
                            </td>
                            <td>#<?= $row['id_factura'] ?? 'N/A' ?></td>
                            <td style="font-size: 12px; color: #7f8c8d;">
                                <?= date("d/m/Y h:i A", strtotime($row['fecha_registro'])) ?>
                            </td>
                        </tr>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <tr>
                            <td colspan="6" style="padding: 30px; text-align: center; color: #7f8c8d;">
                                No se encontraron registros de pagos con los filtros seleccionados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px;">
            <button onclick="window.print()" class="btn_imprimir" style="background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <span class="material-symbols-outlined">print</span> Imprimir Mi Historial
            </button>
        </div>
    </div>
</body>
</html>