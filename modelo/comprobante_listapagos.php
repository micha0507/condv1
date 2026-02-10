<?php
include './conexion.php';

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

// 2. Obtener listado de pagos con filtros aplicados
$where = [];

// Filtro por estado
if (!empty($_GET['status'])) {
    $status = $conexion->real_escape_string($_GET['status']);
    $where[] = "pa.status = '$status'";
}

// Filtro por fecha
if (!empty($_GET['fecha'])) {
    $fecha = $conexion->real_escape_string($_GET['fecha']);
    $where[] = "DATE(pa.fecha) = '$fecha'";
}

// Filtro por propietario
if (!empty($_GET['propietario'])) {
    $propietario = $conexion->real_escape_string($_GET['propietario']);
    $where[] = "(p.nombre LIKE '%$propietario%' OR p.apellido LIKE '%$propietario%')";
}

// Filtro por período (basado en la tabla facturas)
if (!empty($_GET['periodo'])) {
    $periodo = $conexion->real_escape_string($_GET['periodo']);
    $where[] = "f.periodo = '$periodo'";
}

// Si no hay filtros, aplicamos la fecha de hoy por defecto para no saturar el PDF
if (empty($where)) {
    $fecha_hoy = date('Y-m-d');
    $where[] = "DATE(pa.fecha) = '$fecha_hoy'";
}

$where_sql = "WHERE " . implode(" AND ", $where);

$sql = "
    SELECT p.nombre, p.apellido, p.rif, pa.referencia, pa.monto, pa.fecha, pa.status,
           r.nro AS num_residencia
    FROM pagos pa
    INNER JOIN propietario p ON pa.id_propietario = p.id
    LEFT JOIN residencias r ON p.id = r.id_propietario
    LEFT JOIN facturas f ON pa.factura_afectada = f.id_factura
    $where_sql
    ORDER BY pa.fecha DESC
";
$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Pagos - <?php echo $nombre_condominio; ?></title>
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

        .header h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 22px;
        }

        .header p {
            margin: 4px 0 0 0;
            font-size: 14px;
            color: #666;
        }

        .header h1 {
            position: absolute;
            right: 35px;
            top: 25px;
            margin: 0;
            color: #4CAF50;
            font-size: 28px;
            font-weight: bold;
        }

        /* Cuerpo del Reporte */
        .body-content {
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        th {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border: 1px solid #c8e6c9;
            text-align: left;
        }

        td {
            padding: 10px;
            border: 1px solid #eee;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        .monto {
            font-weight: bold;
            text-align: right;
            color: #27ae60;
        }

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

        /* Botón de Impresión */
        .btn-flotante {
            text-align: center;
            margin-bottom: 20px;
        }

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

        .btn-back {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Lógica de Impresión (Estilo index_admin) */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .contenedor {
                border: none;
                box-shadow: none;
                max-width: 100%;
            }

            .btn-flotante {
                display: none;
            }

            th {
                -webkit-print-color-adjust: exact;
                background-color: #e8f5e9 !important;
            }
        }
    </style>
</head>

<body>

    <div class="contenedor">
        <div class="header">
            <h2><?php echo $nombre_condominio; ?></h2>
            <p><strong>RIF:</strong> <?php echo $rif_admin; ?></p>
            <p><?php echo $direccion_condominio; ?></p>
            <h1>LISTADO DE PAGOS</h1>
        </div>

        <div class="body-content">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Propietario</th>
                        <th>Residencia</th>
                        <th>Referencia</th>
                        <th>Estado</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . date("d/m/Y", strtotime($row['fecha'])) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nombre'] . " " . $row['apellido']) . "<br><small style='color:gray'>" . $row['rif'] . "</small></td>";
                            echo "<td style='text-align:center'><strong>" . (!empty($row['num_residencia']) ? $row['num_residencia'] : 'N/A') . "</strong></td>";
                            echo "<td style='font-family: monospace;'>" . htmlspecialchars($row['referencia']) . "</td>";
                            echo "<td><span class='status'>" . htmlspecialchars($row['status']) . "</span></td>";
                            echo "<td class='monto'>" . number_format($row['monto'], 2, ',', '.') . " Bs</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center;'>No se encontraron pagos registrados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            Reporte oficial generado el <?php date_default_timezone_set('America/Caracas');
                                        echo date('d/m/Y h:i A'); ?>
            <br>Este documento es el comprobante oficial del listadode pagos del condominio <?php echo $nombre_condominio; ?>.
        </div>

    </div>
    <div class="btn-flotante"><br>
        <button class="btn-imprimir" onclick="window.print()">
            Generar PDF / Imprimir Listado
        </button>

    </div>
</body>

</html>
<?php $conexion->close(); ?>