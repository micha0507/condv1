<?php
session_start();
include './conexion.php';

// 1. Obtener datos del condominio
$sql_condominio = "SELECT rif_admin, nombre_condominio, direccion_condominio, nombre_completo_admin, rol_admin FROM administrador LIMIT 1";
$result_condominio = $conexion->query($sql_condominio);
$condo = $result_condominio->fetch_assoc();

// 2. Obtener filtro de búsqueda si existe
$search = isset($_GET['search']) ? $conexion->real_escape_string($_GET['search']) : '';
$where = "";
if (!empty($search)) {
    $where = "WHERE rif LIKE '%$search%' OR nombre LIKE '%$search%' OR apellido LIKE '%$search%' OR usuario LIKE '%$search%'";
}

$sql_propietarios = "SELECT * FROM propietario $where ORDER BY id DESC";
$result_propietarios = $conexion->query($sql_propietarios);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Listado de Propietarios</title>
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
                <p><strong>Registros:</strong> <?php echo $result_propietarios->num_rows; ?></p>
            </div>
        </div>

        <h3 class="report-title">Listado de Propietarios Registrados</h3>

        <table>
            <thead>
                <tr>
                    <th>RIF</th>
                    <th>Nombre y Apellido</th>
                    <th>Usuario</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_propietarios->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['rif']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre'] . " " . $row['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($row['usuario']); ?></td>
                        <td><?php echo htmlspecialchars($row['email_propietario']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
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