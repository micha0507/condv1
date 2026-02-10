<?php
session_start();
include './conexion.php';

// Obtener datos del condominio
$sql_condominio = "SELECT rif_admin, nombre_condominio, direccion_condominio, nombre_completo_admin, rol_admin FROM administrador LIMIT 1";
$result_condominio = $conexion->query($sql_condominio);

if ($result_condominio && $result_condominio->num_rows > 0) {
    $condominio = $result_condominio->fetch_assoc();
    $rif_admin = htmlspecialchars($condominio['rif_admin']);
    $nombre_condominio = htmlspecialchars($condominio['nombre_condominio']);
    $direccion_condominio = htmlspecialchars($condominio['direccion_condominio']);
    $nombre_completo_admin = htmlspecialchars($condominio['nombre_completo_admin']);
    $rol_admin = htmlspecialchars($condominio['rol_admin']);
} else {
    $rif_admin = "N/A";
    $nombre_condominio = "N/A";
    $direccion_condominio = "N/A";
    $nombre_completo_admin = "N/A";
}

// Obtener el último gasto eventual registrado
$sql = "SELECT * FROM gastos_eventuales ORDER BY id_gasto DESC LIMIT 1";
$result = $conexion->query($sql);

if ($result && $result->num_rows > 0) {
    $gasto = $result->fetch_assoc();

    // Establecer zona horaria de Caracas, Venezuela
    date_default_timezone_set('America/Caracas');
    $fecha_emision = date('d/m/Y');
    $hora_emision = date('h:i:s A');
    
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Comprobante de Gasto Eventual</title>
        <style>
            body {
                background: #f8f8f8;
            }

            .comprobante {
                font-family: Arial, sans-serif;
                margin: 30px auto;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                box-shadow: 0 2px 8px #eee;
                background: #fff;
                max-width: 700px;
            }

            .comprobante-header {
                background: #f5f5f5;
                padding: 20px 30px;
                border-radius: 8px 8px 0 0;
            }

            .comprobante-header h2 {
                margin: 0;
                color: #333;
            }

            .comprobante-header h1 {
                text-align: right;
                color: #e67e22;
                margin: -40px 0 0 0;
            }

            .comprobante-body {
                padding: 20px 30px 10px 30px;
            }

            .comprobante-footer {
                background: #f5f5f5;
                padding: 10px 30px;
                border-radius: 0 0 8px 8px;
                text-align: center;
                color: #888;
                font-size: 13px;
            }

            @media print {
                .btn-imprimir {
                    display: none !important;
                }

                body {
                    background: #fff;
                }
            }
        </style>
    </head>

    <body>
        <div class="comprobante">
            <div class="comprobante-header">
                <h2><?php echo $nombre_condominio; ?></h2>
                <p style="margin: 2px 0 0 0; font-size: 14px;">RIF: <?php echo $rif_admin; ?></p>
                <p style="margin: 2px 0 0 0; font-size: 14px;"><?php echo $direccion_condominio; ?></p>
                <h1>GASTO EVENTUAL</h1>
            </div>
            <div class="comprobante-body">
                <div style="width: 60%; float: left;">
                    <h3 style="margin-bottom: 5px; color: #555;">Detalle del Gasto</h3>
                    <p style="margin: 2px 0;"><strong>Concepto:</strong> <?php echo htmlspecialchars($gasto['concepto']); ?></p>
                </div>
                <div style="width: 38%; float: right; text-align: right;">
                    <p style="margin: 2px 0;"><strong>Nro. Gasto:</strong> <?php echo htmlspecialchars($gasto['id_gasto']); ?></p>
                    <p style="margin: 2px 0;"><strong>Fecha:</strong> <?php echo htmlspecialchars($gasto['fecha']); ?></p>
                    <p style="margin: 2px 0;"><strong>Hora:</strong> <?php echo $hora_emision; ?></p>
                </div>
                <div style="clear: both;"></div>
                <h3 style="color: #555; margin-bottom: 10px;">Monto</h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 15px;">
                    <tr style="background: #ffe0b2;">
                        <th style="padding: 10px; border: 1px solid #ffcc80; text-align: left;">Concepto</th>
                        <th style="padding: 10px; border: 1px solid #ffcc80; text-align: right;">Monto</th>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #e0e0e0;"><?php echo htmlspecialchars($gasto['concepto']); ?></td>
                        <td style="padding: 10px; border: 1px solid #e0e0e0; text-align: right;">
                            <?php echo number_format($gasto['monto'], 2, ',', '.'); ?> Bs
                        </td>
                    </tr>
                </table>
                <table style="width: 100%; margin-top: 10px; font-size: 14px;">
                    <tr>
                        <td style="padding: 5px 0;">
                            <strong>Responsable:</strong> <?php echo $nombre_completo_admin; ?> <br>
                            <strong>Rol:</strong> <?php echo $rol_admin; ?> <br>
                        </td>
                        <td style="padding: 5px 0; text-align: right;">
                            <strong>Referencia:</strong> <?php echo htmlspecialchars($gasto['categoria']); ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="comprobante-footer">
                Este documento es el comprobante oficial del gasto eventual.
            </div>
            <button class="btn-imprimir" onclick="window.print()" style="margin: 20px auto 20px auto; display: block; padding: 10px 20px; background: #e67e22; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
                Imprimir / Guardar PDF
            </button>
        </div>
    </body>

    </html>
<?php
} else {
    echo "No se encontró ningún gasto eventual registrado.";
}
$conexion->close();
?>