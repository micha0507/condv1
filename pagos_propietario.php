<?php
include './modelo/conexion.php';
session_start();

if (empty($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

function getPropietarioId($conexion)
{
    if (!empty($_SESSION['id_propietario'])) return $_SESSION['id_propietario'];
    if (!empty($_SESSION['id'])) return $_SESSION['id'];
    return null;
}

$id_propietario = getPropietarioId($conexion);

// Consultar todo el histórico de pagos del propietario
$sqlHistorial = "SELECT p.*, f.monto as monto_factura_usd 
                 FROM pagos p 
                 LEFT JOIN facturas f ON p.factura_afectada = f.id_factura 
                 WHERE p.id_propietario = ? 
                 ORDER BY p.fecha_registro DESC";

$stmt = $conexion->prepare($sqlHistorial);
$stmt->bind_param("i", $id_propietario);
$stmt->execute();
$historial = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pagos | Conjunto Residencial</title>

    <link rel="icon" href="/img/ico_condo.ico">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Agregamos jQuery para el modal -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        :root {
            --primary: #27ae60;
            --secondary: #2c3e50;
            --bg-color: #f4f7f6;
            --text-main: #333;
            --card-bg: #fff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            color: var(--text-main);
        }

        .container {
            max-width: 95%;
            /* Aumentamos al 95% para aprovechar el ancho */
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 30px;
            /* Aumentamos un poco el padding interno */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            width: 100%;
            /* Aseguramos que ocupe todo el contenedor */
            box-sizing: border-box;
            /* Esto evita que el padding rompa el ancho */
            margin: 0 auto;
            /* Centrado horizontal */
        }

        /* Opcional: Para que la tabla se vea mejor en pantallas grandes */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .header-acciones {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }

        .header-acciones h3 {
            margin: 0;
            color: var(--secondary);
            font-size: 1.4rem;
        }

        .btn-pdf {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .btn-pdf:hover {
            background: #c0392b;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: var(--secondary);
            color: white;
            font-weight: 500;
        }

        tbody tr:hover {
            background-color: #f9f9f9;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-validado {
            background: #d4edda;
            color: #155724;
        }

        .status-pendiente {
            background: #fff3cd;
            color: #856404;
        }

        .status-rechazado {
            background: #fadbd8;
            color: #721c24;
        }
    </style>
</head>

<body>

    <?php include 'navbar_propietario.php'; ?>

    <div class="container">
        <div class="card">
            <div class="header-acciones">
                <h3><span class="material-symbols-outlined" style="vertical-align: bottom;">history</span> Historial de Pagos</h3>

                <!-- Botón actualizado para abrir el modal -->
                <button type="button" id="btnShowPrintModal" class="btn-pdf">
                    <span class="material-symbols-outlined">picture_as_pdf</span> Generar Reporte PDF
                </button>
            </div>

            <div id="reportePDF">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha de Pago</th>
                                <th>Referencia</th>
                                <th>Factura Afectada</th>
                                <th>Monto Pagado (Bs)</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($historial->num_rows > 0): ?>
                                <?php while ($row = $historial->fetch_assoc()):
                                    $clase_estado = 'status-pendiente';
                                    if (strtolower($row['status']) == 'validado') $clase_estado = 'status-validado';
                                    if (strtolower($row['status']) == 'rechazado') $clase_estado = 'status-rechazado';
                                ?>
                                    <tr>
                                        <td><?= date("d/m/Y", strtotime($row['fecha'])) ?></td>
                                        <td><strong><?= htmlspecialchars($row['referencia']) ?></strong></td>
                                        <td>#<?= $row['factura_afectada'] ?></td>
                                        <td style="font-weight: 600;"><?= number_format($row['monto'], 2, ',', '.') ?> Bs</td>
                                        <td>
                                            <span class="status-badge <?= $clase_estado ?>">
                                                <?= htmlspecialchars($row['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #7f8c8d; padding: 20px;">
                                        Aún no existen registros de pago en su historial.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL IDÉNTICO AL DE AÑADIR MIEMBRO -->
    <div id="modalFondo" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999;"></div>
    <div id="modalPrint" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); z-index: 1000; text-align: center; width: 350px;">
        <h2 style="margin: 0 0 10px 0; color: #4CAF50;">Reporte Generado Exitosamente</h2>
        <p style="color: #666; font-size: 14px;">¿Deseas emitir el PDF de tu historial de pagos?</p>
        <div style="margin-top: 25px; display: flex; justify-content: center; gap: 10px;">
            <button id="confirmarPrint" style="padding: 10px 20px; background-color: #1ecaf5; color: white; border: none; border-radius: 5px; cursor: pointer;">Imprimir</button>
            <button id="cancelarPrint" style="padding: 10px 20px; background-color: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancelar</button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Abrir el modal de impresión al dar clic al botón de PDF
            $('#btnShowPrintModal').on('click', function() {
                $('#modalFondo').fadeIn();
                $('#modalPrint').fadeIn();
            });

            // Cerrar el modal
            $('#cancelarPrint, #modalFondo').on('click', function() {
                $('#modalFondo').fadeOut();
                $('#modalPrint').fadeOut();
            });

            // Confirmar y redirigir al comprobante para imprimir
            $('#confirmarPrint').on('click', function() {
                // Abre el archivo del comprobante en una nueva pestaña
                window.open('modelo/comprobante_historial_pagos.php', '_blank');

                // Cierra el modal
                $('#modalFondo').fadeOut();
                $('#modalPrint').fadeOut();
            });
        });
    </script>
</body>

</html>