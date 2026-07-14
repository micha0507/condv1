<?php
include './conexion.php';
session_start();

if (empty($_SESSION['id'])) {
    die("Acceso denegado.");
}

function getPropietarioId($conexion) {
    if (!empty($_SESSION['id_propietario'])) return $_SESSION['id_propietario'];
    if (!empty($_SESSION['id'])) return $_SESSION['id'];
    return null;
}

$id_propietario = getPropietarioId($conexion);

// 1. Obtener datos del condominio/administrador
$sql_condominio = "SELECT rif_admin, nombre_condominio, direccion_condominio FROM administrador LIMIT 1";
$result_condominio = $conexion->query($sql_condominio);

if ($result_condominio && $result_condominio->num_rows > 0) {
    $condominio = $result_condominio->fetch_assoc();
    $rif_admin = htmlspecialchars($condominio['rif_admin']);
    $nombre_condominio = htmlspecialchars($condominio['nombre_condominio']);
    $direccion_condominio = htmlspecialchars($condominio['direccion_condominio']);
} else {
    $rif_admin = "N/A";
    $nombre_condominio = "N/A";
    $direccion_condominio = "N/A";
}

// 2. Obtener datos del Propietario actual
$sql_prop = "SELECT p.nombre, p.apellido, p.rif, r.nro AS num_residencia 
             FROM propietario p 
             LEFT JOIN residencias r ON p.id = r.id_propietario 
             WHERE p.id = ?";
$stmt_prop = $conexion->prepare($sql_prop);
$stmt_prop->bind_param("i", $id_propietario);
$stmt_prop->execute();
$propietario = $stmt_prop->get_result()->fetch_assoc();

// 3. Consultar su historial de pagos
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
    <title>Historial de Pagos - <?php echo $nombre_condominio; ?></title>
    <style>
        /* Estilos base de comprobante_pago.php */
        body {
            background: #f8f8f8;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .contenedor {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        /* Encabezado Estilo Comprobante */
        .header {
            background: #f5f5f5;
            padding: 25px 35px;
            border-bottom: 3px solid #4CAF50;
            position: relative;
        }

        .header h2 { margin: 0; color: #2c3e50; font-size: 22px; }
        .header p { margin: 4px 0 0 0; font-size: 14px; color: #666; }
        .header h1 {
            position: absolute;
            right: 35px;
            top: 25px;
            margin: 0;
            color: #4CAF50;
            font-size: 28px;
            font-weight: bold;
        }

        /* Datos del Propietario */
        .info-propietario {
            background: #e8f5e9;
            padding: 15px 35px;
            border-bottom: 1px solid #c8e6c9;
            display: flex;
            justify-content: space-between;
        }
        .info-propietario p { margin: 5px 0; font-size: 14px; color: #2e7d32; }

        /* Cuerpo del Reporte */
        .body-content { padding: 30px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
        th {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border: 1px solid #c8e6c9;
            text-align: left;
        }
        td { padding: 10px; border: 1px solid #eee; }
        tr:nth-child(even) { background-color: #fafafa; }

        .monto { font-weight: bold; text-align: right; color: #27ae60; }
        .status {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            background: #eee;
            text-transform: uppercase;
        }

        /* Footer */
        .footer {
            background: #f5f5f5;
            padding: 15px;
            text-align: center;
            color: #888;
            font-size: 12px;
        }

        /* Botones y Lógica de Impresión */
        .btn-flotante { text-align: center; margin-bottom: 20px; }
        .btn-imprimir {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        @media print {
            body { background: white; padding: 0; }
            .contenedor { border: none; box-shadow: none; max-width: 100%; }
            .btn-flotante { display: none; }
            th { -webkit-print-color-adjust: exact; background-color: #e8f5e9 !important; }
            .info-propietario { -webkit-print-color-adjust: exact; background-color: #e8f5e9 !important; }
        }
    </style>
</head>
<body>

    <div class="contenedor">
        <div class="header">
            <h2><?php echo $nombre_condominio; ?></h2>
            <p><strong>RIF:</strong> <?php echo $rif_admin; ?></p>
            <p><?php echo $direccion_condominio; ?></p>
            <h1>HISTORIAL DE PAGOS</h1>
        </div>

        <div class="info-propietario">
            <div>
                <p><strong>Propietario:</strong> <?= htmlspecialchars($propietario['nombre'] . ' ' . $propietario['apellido']) ?></p>
                <p><strong>RIF/Cédula:</strong> <?= htmlspecialchars($propietario['rif']) ?></p>
            </div>
            <div>
                <p><strong>Nro. Residencia:</strong> <?= htmlspecialchars($propietario['num_residencia'] ?? 'N/A') ?></p>
                <p><strong>Fecha de Emisión:</strong> <?= date('d/m/Y') ?></p>
            </div>
        </div>

        <div class="body-content">
            <table>
                <thead>
                    <tr>
                        <th>Fecha de Pago</th>
                        <th>Referencia</th>
                        <th>Factura Afectada</th>
                        <th>Estado</th>
                        <th style="text-align: right;">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($historial && $historial->num_rows > 0) {
                        while ($row = $historial->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . date("d/m/Y", strtotime($row['fecha'])) . "</td>";
                            echo "<td style='font-family: monospace; font-weight: bold;'>" . htmlspecialchars($row['referencia']) . "</td>";
                            echo "<td>#" . htmlspecialchars($row['factura_afectada']) . "</td>";
                            echo "<td><span class='status'>" . htmlspecialchars($row['status']) . "</span></td>";
                            echo "<td class='monto'>" . number_format($row['monto'], 2, ',', '.') . " Bs</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center;'>No se encontraron pagos registrados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            Reporte personal generado el <?php date_default_timezone_set('America/Caracas'); echo date('d/m/Y h:i A'); ?>
            <br>Este documento sirve como resumen del estado de cuenta con la administración de <?php echo $nombre_condominio; ?>.
        </div>
    </div>
    
    <div class="btn-flotante"><br>
        <button class="btn-imprimir" onclick="window.print()">
            Generar PDF / Imprimir Historial
        </button>
    </div>
</body>
</html>
<?php $conexion->close(); ?>