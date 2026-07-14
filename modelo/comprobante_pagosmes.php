<?php
session_start();
include './conexion.php';

// Tipo de reporte: 'pagos' (por defecto, para no romper enlaces existentes) o 'gastos'
$tipo = (isset($_GET['tipo']) && $_GET['tipo'] === 'gastos') ? 'gastos' : 'pagos';

// 1. Obtener y sanear el mes/año a reportar (por defecto el mes actual)
$mes = isset($_GET['mes']) ? str_pad(preg_replace('/[^0-9]/', '', $_GET['mes']), 2, '0', STR_PAD_LEFT) : date('m');
$anio = isset($_GET['anio']) ? preg_replace('/[^0-9]/', '', $_GET['anio']) : date('Y');
if ($mes === '' || (int)$mes < 1 || (int)$mes > 12) {
    $mes = date('m');
}
if ($anio === '') {
    $anio = date('Y');
}

$meses_espanol = [
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];
$nombre_mes = $meses_espanol[$mes] ?? $mes;

// 2. Obtener datos del condominio
$sql_condominio = "SELECT rif_admin, nombre_condominio, direccion_condominio, nombre_completo_admin, rol_admin FROM administrador LIMIT 1";
$result_condominio = $conexion->query($sql_condominio);
$condo = $result_condominio->fetch_assoc();

// 3. Consultar los datos según el tipo de reporte (sentencia preparada en ambos casos)
$total_montos = 0;
$filas = [];

if ($tipo === 'gastos') {
    $sql = "SELECT id_gasto, concepto, categoria, fecha, monto 
            FROM gastos_eventuales 
            WHERE MONTH(fecha) = ? AND YEAR(fecha) = ?
            ORDER BY fecha DESC, id_gasto DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $mes, $anio);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($fila = $result->fetch_assoc()) {
        $filas[] = $fila;
        $total_montos += $fila['monto'];
    }
    $stmt->close();
    $titulo_reporte = 'Listado de Gastos del Mes';
} else {
    $sql = "SELECT id, fecha_registro, monto, referencia 
            FROM pagos 
            WHERE MONTH(fecha_registro) = ? AND YEAR(fecha_registro) = ?
            ORDER BY fecha_registro DESC, id DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $mes, $anio);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($fila = $result->fetch_assoc()) {
        $filas[] = $fila;
        $total_montos += $fila['monto'];
    }
    $stmt->close();
    $titulo_reporte = 'Listado de Pagos del Mes';
}

$total_registros = count($filas);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($titulo_reporte); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
        }

        .comprobante-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #4CAF50;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .condo-info h2 {
            margin: 0;
            color: #333;
            font-size: 20px;
        }

        .condo-info p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }

        .report-title {
            text-align: center;
            color: #4CAF50;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #f8f9fa;
            color: #333;
            padding: 12px;
            border: 1px solid #dee2e6;
            text-align: left;
            font-size: 13px;
        }

        td {
            padding: 10px;
            border: 1px solid #dee2e6;
            font-size: 13px;
            color: #555;
        }

        tfoot td {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .btn-imprimir {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto;
        }

        @media print {
            .btn-imprimir {
                display: none;
            }

            .comprobante-container {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>

<body>
    <div class="comprobante-container">
        <div class="header">
            <div class="condo-info">
                <h2><?php echo htmlspecialchars($condo['nombre_condominio']); ?></h2>
                <p><strong>RIF:</strong> <?php echo htmlspecialchars($condo['rif_admin']); ?></p>
                <p><?php echo htmlspecialchars($condo['direccion_condominio']); ?></p>
            </div>
            <div style="text-align: right;">
                <p><strong>Fecha:</strong> <?php echo date('d/m/Y'); ?></p>
                <p><strong>Periodo:</strong> <?php echo htmlspecialchars($nombre_mes . ' ' . $anio); ?></p>
                <p><strong>Registros:</strong> <?php echo $total_registros; ?></p>
            </div>
        </div>

        <h3 class="report-title"><?php echo htmlspecialchars($titulo_reporte . ': ' . $nombre_mes . ' ' . $anio); ?></h3>

        <table>
            <?php if ($tipo === 'gastos'): ?>
                <thead>
                    <tr>
                        <th>Nro.</th>
                        <th>Concepto</th>
                        <th>Categoría</th>
                        <th>Fecha</th>
                        <th>Monto (Bs)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_registros > 0): ?>
                        <?php foreach ($filas as $fila): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila['id_gasto']); ?></td>
                                <td><?php echo htmlspecialchars($fila['concepto']); ?></td>
                                <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
                                <td><?php echo htmlspecialchars($fila['fecha']); ?></td>
                                <td><?php echo number_format($fila['monto'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">No se encontraron gastos para este mes.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">Total:</td>
                        <td><?php echo number_format($total_montos, 2); ?> Bs</td>
                    </tr>
                </tfoot>
            <?php else: ?>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha de Registro</th>
                        <th>Monto (Bs)</th>
                        <th>Referencia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_registros > 0): ?>
                        <?php foreach ($filas as $fila): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fila['id']); ?></td>
                                <td><?php echo htmlspecialchars($fila['fecha_registro']); ?></td>
                                <td><?php echo number_format($fila['monto'], 2); ?></td>
                                <td><?php echo htmlspecialchars($fila['referencia']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center;">No se encontraron pagos para este mes.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">Total:</td>
                        <td colspan="2"><?php echo number_format($total_montos, 2); ?> Bs</td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>

        <div class="footer">
            <strong>Responsable:</strong> <?php echo htmlspecialchars($condo['nombre_completo_admin']); ?> (<?php echo htmlspecialchars($condo['rol_admin']); ?>)
            <br>Reporte oficial generado el <?php date_default_timezone_set('America/Caracas');
                                            echo date('d/m/Y h:i A'); ?>

        </div>

        <button class="btn-imprimir" onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>
</body>

</html>